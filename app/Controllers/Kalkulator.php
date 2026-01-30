<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Kalkulator extends BaseController
{

    public function __construct()
    {
        helper('auth');
    }

    public function hitungBmi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $tinggi = (float) $this->request->getPost('tinggi'); // cm
        $berat  = (float) $this->request->getPost('berat');

        if ($tinggi <= 0 || $berat <= 0) {
            return $this->response->setJSON([
                'bmi' => null,
                'kategori' => '-',
                'csrfHash' => csrf_hash()
            ]);
        }

        $tinggiMeter = $tinggi / 100;
        $bmi = round($berat / ($tinggiMeter * $tinggiMeter), 2);

        if ($bmi < 18.5) {
            $kategori = 'Kurus';
        } elseif ($bmi <= 24.9) {
            $kategori = 'Normal';
        } elseif ($bmi <= 29.9) {
            $kategori = 'Overweight';
        } else {
            $kategori = 'Obesitas';
        }

        return $this->response->setJSON([
            'bmi'       => $bmi,
            'kategori'  => $kategori,
            'csrfHash'  => csrf_hash()
        ]);
    }

    public function hitung()
    {
        // Wajib AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $type     = $this->request->getPost('type');
        $nilai    = (float) $this->request->getPost('nilai');
        $kategori = $this->request->getPost('kategori');
        $gender = $this->request->getPost('gender');

        $hasil = 0;

        switch ($type) {
            case 'lari_12':
                $value = 0;
                if ($gender == "wanita") {
                    $value = 3100;
                } else {
                    $value = 3600;
                }
                if ($nilai >= $value) {
                    $hasil = 100;
                } else {
                    $hasil = floor(($nilai / $value) * 100);
                    if ($hasil > 100) {
                        $hasil = 100;
                    }
                }
                break;
            case 'pull_up':
                if ($nilai >= 17) {
                    $hasil = 100;
                } else {
                    $hasil = floor(($nilai / 17) * 100);
                    if ($hasil > 100) {
                        $hasil = 100;
                    }
                }
                break;
            case 'chinning':
                if ($nilai >= 72) {
                    $hasil = 100;
                } else {
                    $hasil = floor(($nilai / 72) * 100);
                    if ($hasil > 100) {
                        $hasil = 100;
                    }
                }
                break;
            case 'sit_up':
                $value = 0;
                if ($gender == "wanita") {
                    $value = 50;
                } else {
                    $value = 40;
                }
                if ($nilai >= $value) {
                    $hasil = 100;
                } else {
                    $hasil = floor(($nilai / $value) * 100);
                    if ($hasil > 100) {
                        $hasil = 100;
                    }
                }
                break;
            case 'push_up':
                $value = 0;
                if ($gender == "wanita") {
                    $value = 40;
                } else {
                    $value = 42;
                }
                $hasil = floor(($nilai / $value) * 100);
                if ($hasil > 100) {
                    $hasil = 100;
                }
                break;
            case 'shuttle_run':
                $value = 0;
                if ($gender == "wanita") {
                    $value = 17;
                } else {
                    $value = 15;
                }
                $hasil = floor(($value / $nilai) * 100);
                // if ($hasil > 100) {
                //     $hasil = 100;
                // }
                break;
            case 'renang':
                $value = 0;
                if ($gender == "wanita") {
                    $value = 20;
                } else {
                    $value = 14;
                }
                $hasil = floor(($value / $nilai) * 100);
                // if ($hasil > 100) {
                //     $hasil = 100;
                // }
                break;

            default:
                return $this->response->setJSON([
                    'error' => 'Type tidak dikenali',
                    'csrfHash' => csrf_hash()
                ]);
        }

        return $this->response->setJSON([
            'nilai'     => (int) $hasil,
            'csrfHash'  => csrf_hash()
        ]);
    }
}
