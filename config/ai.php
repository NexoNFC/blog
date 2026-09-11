<?php

return [
    'api_key' => env('AI_API_KEY'),
    'base_url' => env('AI_API_BASE_URL', 'https://api.deepseek.com'),
    'model' => env('AI_MODEL', 'deepseek-chat'),
    'timeout' => (int) env('AI_TIMEOUT', 45),
];
