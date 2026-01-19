<?php

namespace App\Models;

use CodeIgniter\Model;

class TryoutSoalModel extends Model
{
    protected $table = 'tryout_soal';
    protected $primaryKey       = 'id';
    protected $allowedFields = [
        'tryout_id',
        'pertanyaan',
        'gambar_soal',
        'opsi_A', 'opsi_B', 'opsi_C', 'opsi_D', 'opsi_E',
        'gambar_opsi_A', 'gambar_opsi_B', 'gambar_opsi_C',
        'gambar_opsi_D', 'gambar_opsi_E',
        'jawaban_benar'
    ];

    public function getSoalTryout($id)
    {
        return $this->where('tryout_id', $id)
                        ->findAll();
    }

    public function randomByTryout($id)
    {
        return $this->where('tryout_id', $id)
                    ->orderBy('RAND()')
                    ->findAll();
    }

    public function countByTryout($tryoutId)
    {
        return $this->where('tryout_id', $tryoutId)->countAllResults();
    }
}
