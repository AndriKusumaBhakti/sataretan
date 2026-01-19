<?php

namespace App\Controllers;

use App\Models\MateriModel;

class Video extends BaseController
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
            $data['video'] = $this->materiModel->where('kategori', $kategori)->where('tipe', 'video')->findAll();
        } else {
            $data['video'] = $this->materiModel->where('tipe', 'video')->findAll();
        }

        $data['kategori'] = $kategori;
        $data['isGuruOrAdmin'] = isGuruOrAdmin();
        return view('video/index', $data);
    }

    // Preview materi interaktif di modal
    public function view($kategori, $tipe, $id)
    {
        $data = $this->baseData();
        $materi = $this->materiModel->find($id);

        if (!$materi || $materi['tipe'] != $tipe) {
            return redirect()->back()->with('errors', ['Video tidak ditemukan']);
        }

        $data['video'] = $materi;
        $data['kategori'] = $kategori;
        return view('video/detail', $data);
    }

    public function create($kategori)
    {
        $data = $this->baseData();
        $data['kategori'] = $kategori;
        return view('video/create', $data);
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
        $path = WRITEPATH . 'uploads/video';

        if ($this->request->getPost('sumber') === 'file') {
            $file = $this->request->getFile('file');
            $fileName = $file->getRandomName();
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

        return redirect()->to(site_url('video/' . $this->request->getPost('kategori')))
            ->with('success', 'Video berhasil ditambahkan');
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
            return redirect()->back()->with('errors', ['Video tidak ditemukan']);
        }

        // Hapus file jika ada
        if ($materi['sumber'] === 'file' && ! empty($materi['file'])) {
            $path = WRITEPATH . 'uploads/video/' . $materi['file'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Hapus data DB
        $this->materiModel->delete($id);

        return redirect()->to(site_url('video/' . $materi['kategori']))
            ->with('success', 'Video berhasil dihapus');
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
            return redirect()->back()->with('errors', ['Video tidak ditemukan']);
        }

        $data['kategori'] = $kategori;
        $data['video'] = $materi;
        return view('video/edit', $data);
    }

    public function update($id)
    {
        // Proteksi role
        if (!isGuruOrAdmin()) {
            return redirect()->back()->with('errors', ['Anda tidak memiliki akses']);
        }

        $materi = $this->materiModel->find($id);
        if (! $materi) {
            return redirect()->back()->with('errors', ['Video tidak ditemukan']);
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
        $path = WRITEPATH . 'uploads/video';
        if ($data['sumber'] === 'file') {
            $file = $this->request->getFile('file');

            if ($file && $file->isValid()) {
                if ($materi['file']) {
                    @unlink($path . '/' . $materi['file']);
                }

                $newName = $file->getRandomName();
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

        return redirect()->to(site_url('video/' . $data['kategori']))
            ->with('success', 'Video berhasil diperbarui');
    }
}
