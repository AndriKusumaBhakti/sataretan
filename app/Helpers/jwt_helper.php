<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT($data)
{
    $config = config('JWT');

    $time = time();

    $payload = [
        'iat'  => $time,
        'exp'  => $time + $config->ttl,
        'data' => $data
    ];

    return JWT::encode($payload, $config->secretKey, $config->algo);
}

function validateJWT($token)
{
    $config = config('JWT');
    if (!$token) {
        return false; // BELUM LOGIN
    }
    try {
        return JWT::decode($token, new Key($config->secretKey, $config->algo));
    } catch (Exception $e) {
        return false;
    }
}
