<?php

return [

    'default_driver' => 'gemini',

    'drivers' => [
        'gemini' => [
            'api_key'     => env('GEMINI_API_KEY'),
            'api_version' => 'v1beta',
            'model'       => 'gemini-2.5-flash-lite'
        ],
    ],

    'agents' => [
        'english_talk' => [
            'name'          => 'English Talk', // необязательное
            'instruction'   => 'english_talk',
            'history_limit' => 20, // необязательное
            'driver'        => 'gemini', // необязательное
        ],
    ],
];
