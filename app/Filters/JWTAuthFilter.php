<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('auth_helper');

        $token = authorization();

        if (!$token) {
            return redirect()->to(site_url('logout'));
        }

        try {
            validateJWT($token);
            if (!is_login()){
                return redirect()->to(site_url('logout'));
            }
        } catch (\Throwable $e) {
            return redirect()->to(site_url('logout'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // optional
    }
}
