<?php

return [

    /**
     * Standard API authentication.
     */
    'auth' =>
    [
        'user' => 'api',
        'key' => '__KEY_GOES_HERE__',
    ],

    /**
     * As well as standard API user/key based authentication you can
     * also restrict API client connections to IP addresses also.
     */
    'clients' =>
    [
        /**
         * Enable client connection IP authetication.
         */
        'restricted' => false,

        /**
         * Array of allowed client IP addresses.
         */
        'allowed_client_ips' => [
            '127.0.0.1',
        ],
    ]
];
