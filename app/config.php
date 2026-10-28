<?php

declare(strict_types=1);

return [
    'app_name' => 'Solange Box',
    'timezone' => 'Africa/Blantyre',
    'currency' => 'MWK',
    'currency_symbol' => 'MK',
    'payment' => [
        'provider' => 'sandbox',
    ],
    'upload' => [
        'max_bytes' => 4 * 1024 * 1024,
        'mimes' => [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ],
    ],
];
