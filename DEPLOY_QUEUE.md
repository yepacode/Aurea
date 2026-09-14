# DEPLOY_QUEUE — cómo correr la cola de correos en producción

Todos los mailables de Belleza Áurea (excepto los sincrónicos ya en command scheduler)
implementan `ShouldQueue`. Eso significa que ahora los correos se ENCOLAN en la tabla
`jobs` en lugar de enviarse dentro del request HTTP.

Ventaja: el usuario ya no espera a que el correo salga (checkout instantáneo, admin no
se cuelga al cambiar el estado de un pedido).
Requisito: hay que tener un worker corriendo, si no los correos se quedan atascados.

## 1. Verifica la configuración

En `.env` de producción:

```
QUEUE_CONNECTION=database
```

La tabla `jobs` ya está creada por la migración `0001_01_01_000002_create_jobs_table.php`
(viene por defecto en Laravel 12). No hace falta hacer `queue:table` ni migrar de nuevo.

## 2. Opción A — cron cada minuto (recomendada en Hostinger)

En hPanel → Avanzado → Trabajos Cron, agrega:

```
* * * * * cd ~/domains/bellezaaurea.com/public_html && /usr/bin/php artisan queue:work --max-time=55 --tries=3 --stop-when-empty >> /dev/null 2>&1
```

Ajusta la ruta absoluta a `public_html` según tu instalación. `--max-time=55` hace que
el worker se muera cerca del minuto para no chocar con la siguiente ejecución de cron;
`--stop-when-empty` corta al vaciar la cola.

## 3. Opción B — supervisor / systemd (si tienes VPS)

```
php artisan queue:work --tries=3 --sleep=3 --timeout=90
```

Con `queue:listen` es más simple pero menos eficiente (recarga el framework en cada job).

## 4. Fallback aceptable — QUEUE_CONNECTION=sync

Si NO se puede montar un worker, poner en `.env`:

```
QUEUE_CONNECTION=sync
```

Los correos van a salir siempre; el único costo es que el checkout tarda 1-3 s extra
por cada correo (uno al cliente + uno al admin = 2-6 s). Es un fallback razonable
mientras se configura el cron.

## 5. Cómo verificar que la cola está viva

- Tabla `jobs`: `select count(*) from jobs;` — si crece sin parar, el worker está muerto.
- Tabla `failed_jobs`: `select * from failed_jobs order by failed_at desc;` — jobs
  que fallaron 3 veces (SMTP caído, plantilla rota, etc.). Reintentar con
  `php artisan queue:retry all`.
- Log de Laravel: `storage/logs/laravel.log` para stacktraces.

## 6. Después de tocar código de mailables o SMTP en producción

Reinicia el worker para que agarre el nuevo código:

```
php artisan queue:restart
```

El worker actual termina el job actual y se auto-mata; el siguiente cron levantará
uno nuevo con el código fresco.
