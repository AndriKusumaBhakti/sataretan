<?php

namespace App\Models;

use CodeIgniter\Model;

class TryoutAttemptModel extends Model
{
    protected $table = 'tryout_attempts';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'tryout_id',
        'started_at',
        'finished_at',
        'skor_akhir',
        'status'
    ];

    public function getDaftarNilai($tryoutId)
    {
        return $this->db->table('tryout_attempts a')
            ->select('
                a.id,
                a.user_id,
                u.name as nama,
                a.started_at,
                a.finished_at,
                a.skor_akhir,
                a.status
            ')
            ->join('users u', 'u.id = a.user_id')
            ->where('a.tryout_id', $tryoutId)
            ->orderBy('a.skor_akhir', 'DESC')
            ->get()
            ->getResultArray();
    }
}
