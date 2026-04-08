<?php

return [

    'default_driver' => 'gemini',

    'drivers' => [
        'gemini' => [
            'base_url'    => 'https://generativelanguage.googleapis.com',
            'api_version' => 'v1beta',
            'api_key'     => env('GEMINI_API_KEY'),
            'model'       => env('GEMINI_MODEL'),
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

        'term_translator' => [
            'name'          => 'Term Translator',
            'instruction'   => 'term_translator',
        ],

    ],
];
