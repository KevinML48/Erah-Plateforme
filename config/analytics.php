<?php

return [
    'enabled' => env('ANALYTICS_ENABLED', true),

    'gtag' => [
        'id' => env('GOOGLE_ANALYTICS_ID', 'G-H9C6F8VG4D'),
        'anonymize_ip' => env('GOOGLE_ANALYTICS_ANONYMIZE_IP', true),
    ],

    'gtm' => [
        'id' => env('GOOGLE_TAG_MANAGER_ID', ''),
    ],
];
