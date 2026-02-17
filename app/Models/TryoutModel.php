<?php

namespace App\Models;

use CodeIgniter\Model;

class TryoutModel extends Model
{
    protected $table            = 'tryout';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'company_id',
        'judul',
        'program',
        'ujian',
        'kategori',
        'jumlah_soal',
        'durasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];
    protected $useTimestamps    = false;

    public function getTryout($kategori = null, $isGuruOrAdmin = null)
    {
        if ($isGuruOrAdmin) {
            return $this->where('kategori', $kategori)
                ->findAll();
        }
        if ($kategori) {
            return $this->where('kategori', $kategori)
                ->where('status', 'aktif')
                ->findAll();
        }

        return [];
    }

    public function getTryoutStatistik($kategori)
    {
        return $this->db->table('tryout t')
            ->select("
                t.id,
                t.judul,
                t.kategori,
                t.jumlah_soal,
                t.durasi,
                t.tanggal_mulai,
                t.tanggal_selesai,
                t.status,

                COUNT(DISTINCT a.user_id) AS peserta,
                COUNT(a.id) AS attempt,
                COALESCE(ROUND(AVG(a.skor_akhir), 1), 0) AS rata_nilai
            ")
            ->join('tryout_attempts a', 'a.tryout_id = t.id', 'left')
            ->when(!isSuperAdmin(), function ($query) {
                $query->where('company_id', companyId());
            })
            ->where('t.kategori', $kategori)
            ->groupBy('t.id')
            ->orderBy('t.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getStatistikGlobal($isGuruOrAdmin = false)
    {
        $builder = $this->db->table('tryout t')
            ->select("
                COUNT(DISTINCT t.id) AS total_tryout,
                COUNT(DISTINCT a.user_id) AS total_peserta,
                COUNT(a.id) AS total_attempt,
                COALESCE(ROUND(AVG(a.skor_akhir), 1), 0) AS rata_nilai
            ")
            ->join('tryout_attempts a', 'a.tryout_id = t.id', 'left')
            ->when(!isSuperAdmin(), function ($query) {
                $query->where('t.company_id', companyId());
            });

        if (!$isGuruOrAdmin) {
            $builder->where('t.status', 'aktif');
        }

        return $builder->get()->getRowArray();
    }

    public function getGrafikPerKategori($isGuruOrAdmin = false)
    {
        $builder = $this->db->table('tryout t')
            ->select("
            t.kategori,
            COUNT(DISTINCT a.user_id) AS peserta,
            COUNT(a.id) AS attempt,
            COALESCE(ROUND(AVG(a.skor_akhir),1), 0) AS rata_nilai
        ")
            ->join('tryout_attempts a', 'a.tryout_id = t.id', 'left')
            ->when(!isSuperAdmin(), function ($query) {
                $query->where('t.company_id', companyId());
            })
            ->groupBy('t.kategori')
            ->orderBy('t.kategori', 'ASC');

        if (!$isGuruOrAdmin) {
            $builder->where('t.status', 'aktif');
        }

        return $builder->get()->getResultArray();
    }

    public function getStatistikSiswaByProgram(string $program, $kategori)
    {
        return $this->db->table('tryout t')
            ->select("
                t.id,
                t.judul,
                t.kategori,
                t.jumlah_soal,
                t.durasi,
                t.tanggal_mulai,
                t.tanggal_selesai,
                t.status,

                COUNT(DISTINCT a.user_id) AS peserta,
                COUNT(a.id) AS attempt,
                COALESCE(ROUND(AVG(a.skor_akhir), 1), 0) AS rata_nilai
            ")
            ->join('tryout_attempts a', 'a.tryout_id = t.id', 'left')
            ->when(!isSuperAdmin(), function ($query) {
                $query->where('company_id', companyId());
            })
            ->where('t.kategori', $kategori)
            ->where('t.status', 'aktif')
            ->where("JSON_CONTAINS(t.program, '\"{$program}\"')", null, false)
            ->groupBy('t.id')
            ->orderBy('t.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getGrafikBulananKategori()
    {
        $sql = "
    SELECT
        best.bulan,
        t.kategori,
        ROUND(AVG(best.nilai),1) AS rata_nilai
    FROM tryout t
    JOIN (
        SELECT
            ta.user_id,
            ta.tryout_id,
            DATE_FORMAT(ta.started_at,'%Y-%m') AS bulan,
            MAX(ta.skor_akhir) AS nilai
        FROM tryout_attempts ta
        JOIN tryout tr ON tr.id = ta.tryout_id
        WHERE ta.started_at IS NOT NULL
        GROUP BY ta.user_id, ta.tryout_id, bulan
    ) best ON best.tryout_id = t.id
    WHERE 1=1
    ";

        if (!isSuperAdmin())
            $sql .= " AND t.company_id=" . companyId();

        $sql .= "
        GROUP BY best.bulan, t.kategori
        ORDER BY best.bulan ASC
    ";

        return $this->db->query($sql)->getResultArray();
    }
}
