<?php

return [
    'providers' => [
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'api_version' => 'v1beta',
            'model' => 'gemini-3-flash-preview'
        ],
    ],
];
