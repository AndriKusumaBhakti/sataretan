<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $session;
    protected $db;
    protected bool $dbStatus = false;
    protected ?string $dbError = null;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->helpers = ['form', 'url', 'auth_helper', 'jwt', 'text'];
        $this->session = service('session');

        // Inisialisasi database
        try {
            $this->db = \Config\Database::connect();
            $this->dbStatus = true;

        } catch (\Throwable $e) {
            $this->dbStatus = false;
            $this->dbError  = $e->getMessage();
        }

        // Jika DB gagal, tampilkan halaman error
        if (!$this->dbStatus) {
            echo view('errors/db_error', [
                'message' => 'Koneksi database gagal: ' . $this->dbError
            ]);
            exit;
        }
    }

    /**
     * Cek status database
     */
    protected function checkDatabase(): bool
    {
        return $this->dbStatus;
    }

    /**
     * Ambil instance database
     */
    protected function getDB()
    {
        return $this->db;
    }
}
