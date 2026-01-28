<?php

namespace App\Models;
use CodeIgniter\Model;

class MateriModel extends Model
{
    protected $table      = 'materi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['company_id','program', 'judul', 'tipe', 'sumber', 'file', 'link', 'kategori'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
