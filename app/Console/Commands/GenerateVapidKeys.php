<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

/**
 * Genera un par de llaves VAPID (para Web Push) y las escribe en el .env
 * como VAPID_PUBLIC_KEY y VAPID_PRIVATE_KEY. Correr una sola vez por entorno.
 *
 * Uso:
 *   php artisan push:generate-keys
 *   php artisan push:generate-keys --show   (solo imprimir, no tocar .env)
 */
class GenerateVapidKeys extends Command
{
    protected $signature = 'push:generate-keys {--show : Solo mostrar las llaves, no modificar .env}';
    protected $description = 'Genera un par de llaves VAPID para Web Push y las guarda en .env';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $public  = $keys['publicKey'];
        $private = $keys['privateKey'];

        $this->info('Llaves VAPID generadas.');
        $this->line('');
        $this->line('VAPID_PUBLIC_KEY='.$public);
        $this->line('VAPID_PRIVATE_KEY='.$private);
        $this->line('');

        if ($this->option('show')) {
            return self::SUCCESS;
        }

        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            $this->error('No se encontró el archivo .env. Copia las llaves arriba manualmente.');
            return self::FAILURE;
        }

        $env = file_get_contents($envPath);

        $env = $this->setEnvVar($env, 'VAPID_PUBLIC_KEY',  $public);
        $env = $this->setEnvVar($env, 'VAPID_PRIVATE_KEY', $private);
        // subject por defecto si no existe
        if (! preg_match('/^VAPID_SUBJECT=/m', $env)) {
            $env = rtrim($env, "\n")."\nVAPID_SUBJECT=mailto:hola@bellezaaurea.com\n";
        }

        file_put_contents($envPath, $env);

        $this->info('Llaves guardadas en .env. Recuerda correr: php artisan config:clear');
        return self::SUCCESS;
    }

    private function setEnvVar(string $env, string $key, string $value): string
    {
        $line = $key.'='.$value;
        if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $env)) {
            return preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $env);
        }
        return rtrim($env, "\n")."\n".$line."\n";
    }
}
