<?php

$proxies = env('TRUSTED_PROXIES');

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Reverse proxies or load balancers whose X-Forwarded-* headers are trusted.
    | Leave TRUSTED_PROXIES empty when visitors reach the web server directly:
    | trusting every proxy would let clients spoof their IP address and bypass
    | the login and form rate limits.
    |
    */

    'proxies' => match (true) {
        blank($proxies) => null,
        $proxies === '*' => '*',
        default => array_map('trim', explode(',', $proxies)),
    },

];
