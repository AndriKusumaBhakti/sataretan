<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    public array $default = [
        'dsn'       => '',
        'hostname'  => 'localhost',   // XAMPP default
        'username'  => 'root',        // XAMPP default root
        'password'  => '',            // XAMPP default kosong
        'database'  => 'db_sadar_sehat',      // Ganti sesuai database kamu
        'DBDriver' => 'MySQLi',
        'dbprefix'  => '',
        'pconnect'  => FALSE,
        'DBDebug'  => (ENVIRONMENT !== 'production'),          // TRUE = tampilkan error koneksi
        'cache_on'  => FALSE,
        'cachedir'  => '',
        'charset' => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swap_pre' => '',
        'encrypt'  => FALSE,
        'compress' => FALSE,
        'stricton' => FALSE,
        'failover' => array(),
        'save_queries' => TRUE,
        'port'       => 3306,
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Gunakan database development jika environment testing
        if (ENVIRONMENT === 'development') {
            $this->defaultGroup = 'default';
        }
    }
}
