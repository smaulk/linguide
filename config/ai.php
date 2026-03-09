<?php

return [

    'default_driver' => 'gemini',

    'drivers' => [
        'gemini' => [
            'base_url'    => 'https://generativelanguage.googleapis.com',
            'api_key'     => env('GEMINI_API_KEY'),
            'api_version' => 'v1beta',
            'model'       => 'gemini-2.5-flash-lite'
        ],
    ],

    'agents' => [

        'english_talk' => [
            'name'          => 'English Talk', // необязательное
            'instruction'   => 'english_talk',
            // 'instruction'   => 'test',
            'history_limit' => 20, // необязательное
            'driver'        => 'gemini', // необязательное
        ],

        'translator' => [
            'name'          => 'Translator',
            'instruction'   => 'translator',
        ],

    ],
];
