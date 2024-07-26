<?php

namespace App\Helpers;

use Spatie\WebhookServer\WebhookCall;

class WebhookHelper
{
    public const METHOD_DETACH = 'DETACH';
    public const METHOD_ATTACH = 'ATTACH';
    public const METHOD_CREATE = 'CREATE';
    public const METHOD_UPDATE = 'UPDATE';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_SIGN_OUT = 'SIGN_OUT';

    public static function syncData(array $payload): void
    {
        $serviceConfig = config('settings.webhooks.service_name');

        WebhookCall::create()
            ->timeoutInSeconds(3)
            ->maximumTries(5)
            ->url($serviceConfig['app_url'] . '/service-sync-webhook')
            ->payload($payload)
            ->useSecret($serviceConfig['app_secret_key'])
            ->dispatch();
    }
}
