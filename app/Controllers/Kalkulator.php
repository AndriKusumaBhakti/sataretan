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
                if ($kategori === 'tni') {

                    $min = 1531;
                    $max = 3412;
                    $kelipatan = 19;

                    if ($nilai < $min) {
                        $hasil = 0;
                    } else {
                        $hasil = floor(($nilai - $min) / $kelipatan) + 1;
                        $maksNilai = floor(($max - $min) / $kelipatan) + 1;
                        $hasil = min($hasil, $maksNilai);
                    }
                } else {
                    if ($nilai < 1380) {
                        $hasil = 1;
                    } elseif ($nilai > 3444) {
                        $hasil = 100;
                    } else {
                        $hasil = min(
                            100,
                            max(1, floor(($nilai - 1380) / 21) + 2)
                        );
                    }
                }
                break;
            case 'pull_up':
                if ($kategori === 'tni') {
                    if ($nilai < 1) {
                        $hasil = 0;
                    } else {
                        $hasil = 15 + (5 * $nilai);
                        if ($nilai > 17 || $hasil > 100) {
                            $hasil = 100;
                        }
                    }
                } else {
                    if ($nilai < 1) {
                        $hasil = 0;
                    } else {
                        $hasil = 15 + (5 * $nilai);
                        if ($nilai > 17 || $hasil > 100) {
                            $hasil = 100;
                        }
                    }
                }
                break;
            case 'sit_up':
                $x = (int) $nilai;
                if ($kategori === 'tni') {
                    if ($x < 12) {
                        $hasil = 0;
                    } else if ($gender == "wanita") {
                        if ($x >= 34) {
                            $hasil = 100;
                        } elseif ($x >= 27) {
                            $hasil = 65 + (($x - 27) * 5);
                        } elseif ($x >= 13) {
                            $hasil = 9 + (($x - 13) * 4);
                        }
                    } else if ($gender == "pria") {
                        if ($x >= 39) {
                            $hasil = 100;
                        } elseif ($x >= 32) {
                            $hasil = 65 + (($x - 32) * 5);
                        } elseif ($x >= 13) {
                            $hasil = 8 + (($x - 13) * 3);
                        }
                    }
                } else {
                    if ($gender == "wanita") {
                        if ($x < 18) {
                            $hasil = 1;
                        } elseif ($x >= 18 && $x <= 19) {
                            $hasil = 3 * ($x - 18) + 3;
                        } elseif ($x >= 20 && $x <= 26) {
                            $hasil = 4 * ($x - 20) + 10;
                        } elseif ($x >= 27 && $x < 40) {
                            $hasil = 4 * ($x - 27) + 48;
                        } else {
                            $hasil = 100;
                        }
                    } else if ($gender == "pria") {
                        if ($x < 7) {
                            $hasil = 1;
                        } elseif ($x >= 7 && $x <= 23) {
                            $hasil = 2 * ($x - 7) + 2;
                        } elseif ($x >= 24 && $x <= 26) {
                            $hasil = 3 * ($x - 24) + 38;
                        } elseif ($x >= 27 && $x < 40) {
                            $hasil = 4 * ($x - 27) + 48;
                        } else {
                            $hasil = 100;
                        }
                    }
                }
                break;
            case 'lunges':
                $hasil = min($nilai * 2, 100);
                break;
            case 'push_up':
                $hasil = min($nilai * 2, 100);
                break;
            case 'shuttle_run':
                if ($kategori === 'tni') {
                    $x = (int) $nilai;

                    if ($x <= 4) {
                        $hasil = 100;
                    } elseif ($x == 5) {
                        $hasil = 85;
                    } elseif ($x == 6) {
                        $hasil = 57;
                    } elseif ($x == 7) {
                        $hasil = 32;
                    } elseif ($x == 8) {
                        $hasil = 7;
                    } else {
                        $hasil = 0;
                    }
                } else {
                    $hasil = max(0, 100 - ($nilai * 2));
                }
                break;
            case 'renang':
                $hasil = min($nilai * 2, 100);
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
