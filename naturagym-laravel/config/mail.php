<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    */

    'mailers' => [

        'smtp' => [
            'transport'     => 'smtp',
            'scheme'        => env('MAIL_SCHEME'),
            'url'           => env('MAIL_URL'),
            'host'          => env('MAIL_HOST', 'smtp.ionos.es'),
            'port'          => env('MAIL_PORT', 587),
            'encryption'    => env('MAIL_ENCRYPTION', 'tls'),
            'username'      => env('MAIL_USERNAME'),
            'password'      => env('MAIL_PASSWORD'),
            'timeout'       => null,
            'local_domain'  => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),

            // ← Añadido para aceptar certificado autofirmado
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                ],
            ],
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport'   => 'failover',
            'mailers'     => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport'   => 'roundrobin',
            'mailers'     => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'admin@tfgmail.alvaroasir.com'),
        'name'    => env('MAIL_FROM_NAME', 'Naturagym'),
    ],

    'admin_notification_email' => env('ADMIN_NOTIFICATION_EMAIL'),

];
