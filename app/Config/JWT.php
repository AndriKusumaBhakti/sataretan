<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class JWT extends BaseConfig
{
    public string $secretKey;
    public string $algo;
    public int $ttl;

    public function __construct()
    {
        $this->secretKey = env('JWT_SECRET');
        $this->algo      = env('JWT_ALGO', 'HS256');
        $this->ttl       = (int) env('JWT_EXPIRE', 300);
    }
}
