<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this in your .env file, as it will be used to sign
    | your tokens. A helper command is provided for this:
    | `php artisan jwt:secret`
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    | Defaults to 1 hour.
    |
    */

    'ttl' => env('JWT_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Refresh time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | within. Defaults to 2 weeks.
    |
    */

    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm that will be used to sign the token.
    |
    | See here: https://github.com/namshi/jose/tree/master/src/Signer/OpenSSL
    | for possible values.
    |
    */

    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | Required Claims
    |--------------------------------------------------------------------------
    |
    | Specify the required claims that must exist in any token.
    |
    */

    'required_claims' => [
        'iss',
        'iat',
        'exp',
        'nbf',
        'sub',
        'jti',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistent Claims
    |--------------------------------------------------------------------------
    |
    | Specify the claims that should be included in the token payload and
    | also persisted when the token is refreshed.
    |
    */

    'persistent_claims' => [
        // 'sub',
        // 'jti',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lock Subject
    |--------------------------------------------------------------------------
    |
    | This will determine whether a "prv" claim is automatically added to
    | the token. The purpose of this is to ensure that if you have multiple
    | authentication models, a token generated for one model can't be used
    | with another.
    |
    */

    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | Leeway
    |--------------------------------------------------------------------------
    |
    | This property gives the jwt timestamp claims some "leeway".
    | This can be used to account for clock skew between servers.
    |
    */

    'leeway' => env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | In order to invalidate tokens, a blacklist is created. If you do not
    | want to use this feature, you can set this option to false.
    |
    */

    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Grace Period
    |--------------------------------------------------------------------------
    |
    | When a token is invalidated, a grace period is applied to allow for
    | token refreshing. This prevents immediate rejection of requests
    | that are in-flight during token invalidation.
    |
    */

    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | Decrypt Cookies
    |--------------------------------------------------------------------------
    |
    | By default, Laravel encrypts cookies. However, the JWT cookie needs
    | to be unencrypted. This option allows you to decrypt the cookie.
    |
    */

    'decrypt_cookies' => false,

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    |
    | Specify the various providers used throughout the package.
    |
    */

    'providers' => [

        /*
        |--------------------------------------------------------------------------
        | JWT Provider
        |--------------------------------------------------------------------------
        |
        | Specify the provider that is responsible for encoding and decoding
        | the tokens. The default provider uses the tymon/jwt package.
        |
        */

        'jwt' => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,

        /*
        |--------------------------------------------------------------------------
        | Authentication Provider
        |--------------------------------------------------------------------------
        |
        | Specify the provider that is responsible for retrieving and
        | authenticating users.
        |
        */

        'auth' => Tymon\JWTAuth\Providers\Auth\Illuminate::class,

        /*
        |--------------------------------------------------------------------------
        | Storage Provider
        |--------------------------------------------------------------------------
        |
        | Specify the provider that is responsible for storing tokens in
        | the blacklist.
        |
        */

        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,

    ],

];