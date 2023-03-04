<?php

return [
    /*
     * Sets the API URL used to communicate with the Channels API.
     *
     * The MWC API is used as a proxy to the Channels API, to handle authentication.
     */
    'api' => [
        'url'         => defined('MWC_EXTENSIONS_API_URL') ? MWC_EXTENSIONS_API_URL : 'https://api.mwc.secureserver.net/v1',
        'maxAttempts' => 3,
        'retryDelay'  => 30, // in seconds
    ],
];
