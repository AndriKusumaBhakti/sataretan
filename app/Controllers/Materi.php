<?php

namespace App\Controllers;

use App\Models\MateriModel;

class Materi extends BaseController
{
    protected array $menuItems = [];
    protected $materiModel;

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
        $this->materiModel = new MateriModel();
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
        if ($kategori) {
            $data['materi'] = $this->materiModel->where('kategori', $kategori)->where('tipe !=', 'video')->findAll();
        } else {
            $data['materi'] = $this->materiModel->where('tipe !=', 'video')->findAll();
        }

        $data['kategori'] = $kategori;
        $data['isGuruOrAdmin'] = isGuruOrAdmin();
        return view('materi/materi_view', $data);
    }

    // Preview materi interaktif di modal
    public function view($kategori, $tipe, $id)
    {
        $data = $this->baseData();
        $materi = $this->materiModel->find($id);

        if (!$materi || $materi['tipe'] != $tipe) {
            return redirect()->back()->with('errors', ['Materi tidak ditemukan']);
        }

        $data['materi'] = $materi;
        $data['kategori'] = $kategori;
        return view('materi/materi_single_view', $data);
    }

    public function create($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        return view('materi/create_materi', $data);
    }

    public function store()
    {
        // Proteksi role
        if (!isGuruOrAdmin()) {
            return redirect()->back()->with('errors', ['Anda tidak memiliki akses']);
        }

        $rules = [
            'judul'   => 'required|min_length[3]',
            'tipe'    => 'required|in_list[pdf,video,word]',
            'sumber'  => 'required|in_list[file,link]'
        ];

        if ($this->request->getPost('sumber') === 'file') {
            $rules['file'] = 'uploaded[file]|max_size[file,10240]';
        } else {
            $rules['link'] = 'required|valid_url';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $fileName = null;
        $link     = null;

        if ($this->request->getPost('sumber') === 'file') {
            $file = $this->request->getFile('file');
            $fileName = $file->getRandomName();
            $path = WRITEPATH . 'uploads/materi';
            $file->move($path, $fileName);
        }

        if ($this->request->getPost('sumber') === 'link') {
            $link = $this->request->getPost('link');
        }

        $this->materiModel->insert([
            'judul'    => $this->request->getPost('judul'),
            'kategori' => $this->request->getPost('kategori'),
            'tipe'     => $this->request->getPost('tipe'),
            'sumber'   => $this->request->getPost('sumber'),
            'file'     => $fileName,
            'link'     => $link
        ]);

        return redirect()->to(site_url('/materi/' . $this->request->getPost('kategori')))
            ->with('success', 'Materi berhasil ditambahkan');
    }

    public function delete($id)
    {
        // Proteksi role
        if (!isGuruOrAdmin()) {
            return redirect()->back()->with('errors', ['Anda tidak memiliki akses']);
        }

        // Ambil data materi
        $materi = $this->materiModel->find($id);

        if (! $materi) {
            return redirect()->back()->with('errors', ['Materi tidak ditemukan']);
        }

        // Hapus file jika ada
        if ($materi['sumber'] === 'file' && ! empty($materi['file'])) {
            $path = WRITEPATH . 'uploads/materi/' . $materi['file'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

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

        $materi = $this->materiModel->find($id);

        if (! $materi) {
            return redirect()->back()->with('errors', ['Materi tidak ditemukan']);
        }

        $data['kategori'] = $kategori;
        $data['materi'] = $materi;
        return view('materi/edit_materi', $data);
    }

    public function update($id)
    {
        // Proteksi role
        if (!isGuruOrAdmin()) {
            return redirect()->back()->with('errors', ['Anda tidak memiliki akses']);
        }

        $materi = $this->materiModel->find($id);
        if (! $materi) {
            return redirect()->back()->with('errors', ['Materi tidak ditemukan']);
        }

        $data = [
            'judul'    => $this->request->getPost('judul'),
            'kategori' => $this->request->getPost('kategori'),
            'tipe'     => $this->request->getPost('tipe'),
            'sumber'   => $this->request->getPost('sumber'),
            'file'     => $materi['file'],
            'link'     => $materi['link'],
        ];

        // jika upload file baru
        if ($data['sumber'] === 'file') {
            $file = $this->request->getFile('file');

            if ($file && $file->isValid()) {
                if ($materi['file']) {
                    @unlink(WRITEPATH . 'uploads/materi/' . $materi['file']);
                }

                $newName = $file->getRandomName();
                $path = WRITEPATH . 'uploads/materi';
                $file->move($path, $newName);

                $data['file'] = $newName;
                $data['link'] = null;
            }
        }

        // jika link
        if ($data['sumber'] === 'link') {
            $data['link'] = $this->request->getPost('link');
            $data['file'] = null;
        }

        $this->materiModel->update($id, $data);

        return redirect()->to(site_url('/materi/' . $data['kategori']))
            ->with('success', 'Materi berhasil diperbarui');
    }

    public function uu()
    {
        $data = $this->baseData();
        return view('uu_police', $data);
    }
}
