<?php

namespace App\Services;

use App\Models\PushSubscription as PushSubscriptionModel;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription as WebPushSubscription;
use Minishlink\WebPush\WebPush;

/**
 * Envío de Web Push notifications con VAPID.
 *
 * Payload esperado (todos opcionales excepto title/body):
 *   [
 *     'title'   => string,   // requerido
 *     'body'    => string,   // requerido
 *     'image'   => ?string,  // URL absoluta (opcional)
 *     'url'     => ?string,  // URL al hacer clic
 *     'actions' => ?array,   // [{action, title}, ...]
 *   ]
 */
class PushService
{
    /**
     * Envía a UNA suscripción. Si el endpoint responde 404/410 (expiró),
     * la subscripción se borra automáticamente.
     */
    public function send(PushSubscriptionModel $sub, array $payload): bool
    {
        $webPush = $this->makeWebPush();
        if (! $webPush) return false;

        $subscription = WebPushSubscription::create([
            'endpoint'        => $sub->endpoint,
            'publicKey'       => $sub->public_key,
            'authToken'       => $sub->auth_token,
            'contentEncoding' => 'aesgcm',
        ]);

        $body = json_encode($this->normalizePayload($payload));

        $webPush->queueNotification($subscription, $body);

        $ok = true;
        foreach ($webPush->flush() as $report) {
            if (! $report->isSuccess()) {
                $status = $report->getResponse()?->getStatusCode();
                if ($status === 404 || $status === 410) {
                    // Suscripción muerta → eliminar
                    $sub->delete();
                } else {
                    Log::warning('WebPush send failed', [
                        'sub_id' => $sub->id,
                        'status' => $status,
                        'reason' => $report->getReason(),
                    ]);
                    $ok = false;
                }
            }
        }
        return $ok;
    }

    /**
     * Envía a todas las suscripciones activas (broadcast).
     * Devuelve [enviadas, fallidas, expiradas].
     */
    public function broadcastToAll(array $payload): array
    {
        return $this->broadcast(PushSubscriptionModel::query(), $payload);
    }

    /**
     * Envía a todas las suscripciones de un customer (todos sus navegadores).
     */
    public function sendToCustomer(int $customerId, array $payload): array
    {
        return $this->broadcast(
            PushSubscriptionModel::query()->where('customer_id', $customerId),
            $payload
        );
    }

    /**
     * Envía a todas las suscripciones cuyos customers coincidan con estos emails.
     * Útil para "back in stock": los avisos se guardan por email; buscamos si
     * ese email tiene un customer con push activo.
     */
    public function sendToEmails(array $emails, array $payload): array
    {
        if (empty($emails)) return [0, 0, 0];

        $query = PushSubscriptionModel::query()
            ->whereHas('customer', fn ($q) => $q->whereIn('email', $emails));

        return $this->broadcast($query, $payload);
    }

    private function broadcast($query, array $payload): array
    {
        $webPush = $this->makeWebPush();
        if (! $webPush) return [0, 0, 0];

        $sent = 0; $failed = 0; $expired = 0;
        $expiredIds = [];
        $body = json_encode($this->normalizePayload($payload));

        // Iteramos en chunks para no volar memoria si hay muchos suscritos.
        $query->chunkById(500, function ($subs) use ($webPush, $body, &$sent, &$failed, &$expired, &$expiredIds) {
            $index = [];
            foreach ($subs as $sub) {
                $subscription = WebPushSubscription::create([
                    'endpoint'        => $sub->endpoint,
                    'publicKey'       => $sub->public_key,
                    'authToken'       => $sub->auth_token,
                    'contentEncoding' => 'aesgcm',
                ]);
                $webPush->queueNotification($subscription, $body);
                $index[$sub->endpoint] = $sub->id;
            }

            foreach ($webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    $sent++;
                } else {
                    $status = $report->getResponse()?->getStatusCode();
                    $endpoint = $report->getRequest()->getUri()->__toString();
                    if ($status === 404 || $status === 410) {
                        $expired++;
                        if (isset($index[$endpoint])) $expiredIds[] = $index[$endpoint];
                    } else {
                        $failed++;
                    }
                }
            }
        });

        if (!empty($expiredIds)) {
            PushSubscriptionModel::whereIn('id', $expiredIds)->delete();
        }

        return [$sent, $failed, $expired];
    }

    private function makeWebPush(): ?WebPush
    {
        $public  = config('services.webpush.vapid.public_key');
        $private = config('services.webpush.vapid.private_key');
        $subject = config('services.webpush.vapid.subject', 'mailto:hola@bellezaaurea.com');

        if (! $public || ! $private) {
            Log::warning('WebPush: faltan VAPID_PUBLIC_KEY / VAPID_PRIVATE_KEY en .env');
            return null;
        }

        return new WebPush([
            'VAPID' => [
                'subject'    => $subject,
                'publicKey'  => $public,
                'privateKey' => $private,
            ],
        ]);
    }

    private function normalizePayload(array $payload): array
    {
        return [
            'title'   => (string) ($payload['title'] ?? 'Belleza Áurea'),
            'body'    => (string) ($payload['body']  ?? ''),
            'image'   => $payload['image']   ?? null,
            'url'     => $payload['url']     ?? '/',
            'actions' => $payload['actions'] ?? [],
        ];
    }
}
