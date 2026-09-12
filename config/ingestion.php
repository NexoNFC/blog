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
        'home_path' => '',
        'item_limit' => (int) env('INGESTION_FESC_LIMIT', 12),
        'listings' => [
            [
                'path' => 'news-bienestar',
                'section' => 'news-bienestar',
                'label' => 'News Bienestar',
                'category' => 'noticia',
            ],
            [
                'path' => 'comunicados',
                'section' => 'comunicados',
                'label' => 'Comunicados',
                'category' => 'comunicado',
            ],
            [
                'path' => 'news-sig',
                'section' => 'news-sig',
                'label' => 'Novedades SIG',
                'category' => 'institucional',
            ],
            [
                'path' => 'news-extension',
                'section' => 'news-extension',
                'label' => 'News Extension',
                'category' => 'noticia',
            ],
        ],
    ],
];
