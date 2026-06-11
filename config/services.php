<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'flexpay' => [
        'merchant' => env('FLEXPAY_MARCHAND'),
        'token' => env('FLEXPAY_API_TOKEN'),
        'gateway_mobile' => env('FLEXPAY_GATEWAY_MOBILE', 'https://backend.flexpay.cd/api/rest/v1/paymentService'),
        'gateway_card' => env('FLEXPAY_GATEWAY_CARD', 'https://cardpayment.flexpay.cd/v1.1/pay'),
        'gateway_check' => env('FLEXPAY_GATEWAY_CHECK', 'https://backend.flexpay.cd/api/rest/v1/check'),
        'frontend_url' => rtrim(env('FRONTEND_URL', 'https://silasmas.com'), '/'),
    ],

    'academy' => [
        'sms_webhook_url' => env('ACADEMY_SMS_WEBHOOK_URL'),
        'whatsapp_webhook_url' => env('ACADEMY_WHATSAPP_WEBHOOK_URL'),
        'admin_alert_email' => env('ACADEMY_ADMIN_ALERT_EMAIL', 'ir-masimango@silasmas.com'),
    ],

];
