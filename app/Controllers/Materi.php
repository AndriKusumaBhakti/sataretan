<?php

namespace App\Controllers;

use Config\Database;
use App\Models\MateriModel;
use App\Models\UserPaketModel;
use App\Models\MateriDetailModel;
use App\Models\ParameterModel;

class Materi extends BaseController
{
    protected array $menuItems = [];
    protected $materiModel;
    protected $userPaketModel;
    protected $materiDetailModel;
    protected $parameter;

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
        $this->materiModel = new MateriModel();
        $this->userPaketModel = new UserPaketModel();
        $this->materiDetailModel = new MateriDetailModel();
        $this->parameter = new ParameterModel();
    }

    private function baseData(): array
    {
        $this->checkDatabase();
        $data = default_parser_item([]); // Pastikan helper ini tersedia
        $data['menuItems'] = $this->menuItems;
        $data['base_url'] = base_url('/');
        return $data;
    }

    public function index($kategori = null)
    {
        $data = $this->baseData();
        $builder = $this->materiModel
            ->when(!isSuperAdmin(), function ($query) {
                $query->where('company_id', companyId());
            })
            ->where('tipe !=', 'video');

        if ($kategori) {
            $builder->where('kategori', $kategori);
        }
        if (!isGuruOrAdmin()) {
            $user = $this->userPaketModel
                ->select('program')
                ->where('user_id', user_id())
                ->first();

            $userProgram = $user['program'] ?? null;

            if (! $userProgram) {
                $data['materi'] = [];
            } else {
                $builder
                    ->groupStart()
                    ->where("JSON_CONTAINS(program, '\"{$userProgram}\"')")
                    ->groupEnd();

                $data['materi'] = $builder->findAll();
            }
        } else {
            $data['materi'] = $builder->findAll();
        }

        $data['kategori'] = $kategori;
        $data['isGuruOrAdmin'] = isGuruOrAdmin();
        return view('materi/materi_view', $data);
    }

    // Preview materi interaktif di modal
    public function view($kategori, $tipe, $id)
    {
        $data = $this->baseData();
        $materiQuery = $this->materiModel
            ->where('id', $id)
            ->where('tipe', $tipe);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $materiQuery->where('company_id', companyId());
        }

        $materi = $materiQuery->first();

        if (!$materi || $materi['tipe'] != $tipe) {
            return redirect()->back()->with('errors', ['Materi tidak ditemukan']);
        }

        $data['materi'] = $materi;
        $data['subMateri'] = $this->materiDetailModel->where('materi_id', $id)->orderBy('urutan', 'ASC')->findAll();
        $data['kategori'] = $kategori;
        return view('materi/materi_single_view', $data);
    }

    public function create($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        $data['program'] = $this->parameter->getValue("program");
        return view('materi/create_materi', $data);
    }

    public function store()
    {
        $rules = [
            'program' => 'required',
            'judul'   => 'required|min_length[3]',
            'tipe'    => 'required|in_list[pdf,video,word]',
            'sumber'  => 'required|in_list[file,link]'
        ];

        $sumber   = $this->request->getPost('sumber');
        $subJudul = $this->request->getPost('sub_judul') ?? [];
        if (count($subJudul) === 0) {
            if ($sumber === 'file') {
                $rules['file'] = 'uploaded[file]|max_size[file,10240]';
            }
            if ($sumber === 'link') {
                $rules['link'] = 'required|valid_url';
            }
        }
        if (count($subJudul) > 0) {
            foreach ($subJudul as $i => $judulSub) {
                $rules["sub_judul.$i"] = 'required|min_length[3]';
                if ($sumber === 'file') {
                    $rules["sub_file.$i"] =
                        'uploaded[sub_file.' . $i . ']|max_size[sub_file.' . $i . ',10240]';
                }
                if ($sumber === 'link') {
                    $rules["sub_link.$i"] = 'required|valid_url';
                }
            }
        }
        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $fileName = null;
        $link     = null;

        $programArray = $this->request->getPost('program');
        $programJson  = json_encode($programArray);

        if (count($subJudul) === 0) {
            if ($sumber === 'file') {
                $file = $this->request->getFile('file');
                $fileName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads/materi', $fileName);
            }
            if ($sumber === 'link') {
                $link = $this->request->getPost('link');
            }
        }

        $db = Database::connect();
        $db->transStart();

        try {

            $materiId = $this->materiModel->insert([
                'company_id'   => companyId(),
                'program'  => $programJson,
                'judul'    => $this->request->getPost('judul'),
                'kategori' => $this->request->getPost('kategori'),
                'tipe'     => $this->request->getPost('tipe'),
                'sumber'   => $sumber,
                'file'     => $fileName,
                'link'     => $link
            ]);

            if (count($subJudul) > 0) {
                if ($sumber === 'file') {
                    $subFiles = $this->request->getFiles()['sub_file'];
                    foreach ($subJudul as $i => $judulSub) {
                        $namaFile = $subFiles[$i]->getRandomName();
                        $subFiles[$i]->move(
                            WRITEPATH . 'uploads/materi/sub',
                            $namaFile
                        );
                        $this->materiDetailModel->insert([
                            'materi_id' => $materiId,
                            'sub_judul' => $judulSub,
                            'file'      => $namaFile,
                            'link'      => null
                        ]);
                    }
                }
                if ($sumber === 'link') {
                    $subLinks = $this->request->getPost('sub_link');
                    foreach ($subJudul as $i => $judulSub) {
                        $this->materiDetailModel->insert([
                            'materi_id' => $materiId,
                            'sub_judul' => $judulSub,
                            'file'      => null,
                            'link'      => $subLinks[$i]
                        ]);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('errors', ['Gagal menyimpan materi']);
            }

            return redirect()->to(site_url('/materi/' . $this->request->getPost('kategori')))
                ->with('success', 'Materi berhasil ditambahkan');
        } catch (\Throwable $e) {

            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with('errors', [$e->getMessage()]);
        }
    }

    public function delete($id)
    {
        // Proteksi role
        if (!isGuruOrAdmin()) {
            return redirect()->back()->with('errors', ['Anda tidak memiliki akses']);
        }

        // Ambil data materi
        $materiQuery = $this->materiModel
            ->where('id', $id);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $materiQuery->where('company_id', companyId());
        }

        $materi = $materiQuery->first();

        if (! $materi) {
            return redirect()->back()->with('errors', ['Materi tidak ditemukan']);
        }

        // Hapus file jika ada
        if ($materi['sumber'] === 'file' && ! empty($materi['file'])) {
            $path = WRITEPATH . 'uploads/materi/' . $materi['file'];
            if (file_exists($path)) {
                unlink($path);
            }

            $pathSub = WRITEPATH . 'uploads/materi/sub/';
            $oldSubs = $this->materiDetailModel->where('materi_id', $id)->findAll();
            foreach ($oldSubs as $sub) {
                if ($sub['file'] && file_exists($pathSub . $sub['file'])) {
                    unlink($pathSub . $sub['file']);
                }
            }
        }


        $this->materiDetailModel->where('materi_id', $id)->delete();

        // Hapus data DB
        $this->materiModel->delete($id);

        return redirect()->to(site_url('/materi/' . $materi['kategori']))
            ->with('success', 'Materi berhasil dihapus');
    }

    public function edit($kategori, $id)
    {
        // Proteksi role
        if (!isGuruOrAdmin()) {
            return redirect()->back()->with('errors', ['Anda tidak memiliki akses']);
        }
        $data = $this->baseData();

        $materiQuery = $this->materiModel
            ->where('id', $id);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $materiQuery->where('company_id', companyId());
        }

        $materi = $materiQuery->first();

        if (! $materi) {
            return redirect()->back()->with('errors', ['Materi tidak ditemukan']);
        }
        $data['program'] = $this->parameter->getValue("program");

        $data['kategori'] = $kategori;
        $data['materi'] = $materi;
        $data['subMateri'] =  $this->materiDetailModel->where('materi_id', $id)->findAll();
        return view('materi/edit_materi', $data);
    }

    public function update($id)
    {
        // Proteksi role
        if (!isGuruOrAdmin()) {
            return redirect()->back()->with('errors', ['Anda tidak memiliki akses']);
        }

        $materiQuery = $this->materiModel
            ->where('id', $id);

        // validasi company untuk non super admin
        if (!isSuperAdmin()) {
            $materiQuery->where('company_id', companyId());
        }

        $materi = $materiQuery->first();

        if (!$materi) {
            return redirect()->back()->with('errors', ['Materi tidak ditemukan']);
        }
        $rules = [
            'program' => 'required',
            'judul'   => 'required|min_length[3]',
            'tipe'    => 'required|in_list[pdf,video,word]',
            'sumber'  => 'required|in_list[file,link]'
        ];

        $sumber   = $this->request->getPost('sumber');
        $subJudul = $this->request->getPost('sub_judul') ?? [];

        if (count($subJudul) === 0) {
            if ($sumber === 'file') {
                $rules['file'] = 'permit_empty|max_size[file,10240]';
            }
            if ($sumber === 'link') {
                $rules['link'] = 'required|valid_url';
            }
        }

        if (count($subJudul) > 0) {
            foreach ($subJudul as $i => $judulSub) {
                $rules["sub_judul.$i"] = 'required|min_length[3]';
                if ($sumber === 'file') {
                    $rules["sub_file.$i"] =
                        'uploaded[sub_file.' . $i . ']|max_size[sub_file.' . $i . ',10240]';
                }

                if ($sumber === 'link') {
                    $rules["sub_link.$i"] = 'required|valid_url';
                }
            }
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $programJson = json_encode($this->request->getPost('program'));

        $data = [
            'program'  => $programJson,
            'judul'    => $this->request->getPost('judul'),
            'kategori' => $this->request->getPost('kategori'),
            'tipe'     => $this->request->getPost('tipe'),
            'sumber'   => $sumber,
            'file'     => null,
            'link'     => null,
        ];

        $pathMain = WRITEPATH . 'uploads/materi/';
        $pathSub  = WRITEPATH . 'uploads/materi/sub/';

        if (count($subJudul) === 0) {
            if ($sumber === 'file') {
                $file = $this->request->getFile('file');
                if ($file && $file->isValid()) {
                    if ($materi['file'] && file_exists($pathMain . $materi['file'])) {
                        unlink($pathMain . $materi['file']);
                    }

                    $newName = $file->getRandomName();
                    $file->move($pathMain, $newName);
                    $data['file'] = $newName;
                } else {
                    $data['file'] = $materi['file']; // pertahankan
                }
            }
            if ($sumber === 'link') {
                $data['link'] = $this->request->getPost('link');
            }
        }

        if (count($subJudul) > 0 && $materi['file']) {
            if (file_exists($pathMain . $materi['file'])) {
                unlink($pathMain . $materi['file']);
            }
        }

        $db = Database::connect();
        $db->transStart();

        try {
            $this->materiModel->update($id, $data);
            $oldSubs = $this->materiDetailModel
                ->where('materi_id', $id)
                ->findAll();

            foreach ($oldSubs as $sub) {
                if ($sub['file'] && file_exists($pathSub . $sub['file'])) {
                    unlink($pathSub . $sub['file']);
                }
            }

            $this->materiDetailModel
                ->where('materi_id', $id)
                ->delete();

            if (count($subJudul) > 0) {
                if ($sumber === 'file') {
                    $subFiles = $this->request->getFiles()['sub_file'];
                    foreach ($subJudul as $i => $judulSub) {
                        $namaFile = $subFiles[$i]->getRandomName();
                        $subFiles[$i]->move($pathSub, $namaFile);
                        $this->materiDetailModel->insert([
                            'materi_id' => $id,
                            'sub_judul' => $judulSub,
                            'file'      => $namaFile,
                            'link'      => null
                        ]);
                    }
                }

                if ($sumber === 'link') {
                    $subLinks = $this->request->getPost('sub_link');
                    foreach ($subJudul as $i => $judulSub) {
                        $this->materiDetailModel->insert([
                            'materi_id' => $id,
                            'sub_judul' => $judulSub,
                            'file'      => null,
                            'link'      => $subLinks[$i]
                        ]);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal memperbarui materi');
            }

            return redirect()->to(site_url('/materi/' . $data['kategori']))
                ->with('success', 'Materi berhasil diperbarui');
        } catch (\Throwable $e) {

            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with('errors', [$e->getMessage()]);
        }
    }

    public function uu()
    {
        $data = $this->baseData();
        return view('uu_police', $data);
    }
}
