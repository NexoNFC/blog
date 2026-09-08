<?php

return [
    'user_agent' => env(
        'INGESTION_USER_AGENT',
        'FESC-InformacionCampus/1.0 (plataforma academica; +https://www.fesc.edu.co)',
    ),
    'timeout' => (int) env('INGESTION_TIMEOUT', 15),
    'retry_times' => 2,
    'retry_sleep_ms' => 500,
    'fesc' => [
        // Categoría real de comunicados institucionales en el portal Joomla FESC.
        'listing_path' => 'comunicados',
        'item_limit' => (int) env('INGESTION_FESC_LIMIT', 8),
    ],
];
