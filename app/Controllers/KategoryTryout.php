<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TryoutCabangModel;
use App\Models\CompanyModel;
use App\Models\ParameterModel;

class KategoryTryout extends BaseController
{
    protected array $menuItems = [];
    protected $tryoutCabangModel;
    protected $cabangModel;
    protected $parameter;

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
        $this->tryoutCabangModel = new TryoutCabangModel();
        $this->cabangModel = new CompanyModel();
        $this->parameter = new ParameterModel();
    }

    private function baseData(): array
    {
        $this->checkDatabase();

        $data = default_parser_item([]);
        $data['menuItems'] = $this->menuItems;
        $data['base_url'] = base_url('/');

        return $data;
    }

    public function index()
    {
        if (!isSuperAdmin()) {
            return redirect()->back()->with('errors', ['Akses ditolak']);
        }

        $data = $this->baseData();
        $data['cabang'] = $this->cabangModel->findAll();

        $data['selected_cabang'] = session()->getFlashdata('selected_cabang');
        return view('kategory-tryout/index', $data);
    }

    public function save()
    {

        if (!isSuperAdmin()) {
            return redirect()->back()->with('errors', ['Akses ditolak']);
        }

        $company_id = $this->request->getPost('cabang_id');

        if (!$company_id) {
            return redirect()->back()->with('errors', ['Cabang harus dipilih']);
        }

        $akademik = $this->request->getPost('akademik') ?? [];
        $psikolog = $this->request->getPost('psikolog') ?? [];
        var_dump($akademik);
        echo '<br>';
        var_dump($psikolog);


        $persenAkademik = $this->request->getPost('persen_akademik') ?? [];
        $modeAkademik   = $this->request->getPost('mode_akademik') ?? [];
        $penilaianAkademik = $this->request->getPost('penilaian_akademik') ?? [];

        $persenPsikolog = $this->request->getPost('persen_psikolog') ?? [];
        $modePsikolog   = $this->request->getPost('mode_psikolog') ?? [];
        $penilaianPsikolog = $this->request->getPost('penilaian_psikolog') ?? [];

        $this->tryoutCabangModel
            ->where('company_id', $company_id)
            ->delete();

        $insertData = [];

        $paramAkademik = $this->parameter->getValue('akademik');
        $paramPsikolog = $this->parameter->getValue('psikolog');

        $mapAkademik = [];
        foreach ($paramAkademik as $p) {
            $mapAkademik[$p['key']] = $p['value'];
        }

        $mapPsikolog = [];
        foreach ($paramPsikolog as $p) {
            $mapPsikolog[$p['key']] = $p['value'];
        }

        foreach ($akademik as $key) {
            $mode = $modeAkademik[$key] ?? null;

            // jika online maka penilaian null
            $penilaian = 'angka';
            if ($mode === 'offline') {
                $penilaian = $penilaianAkademik[$key] ?? null;
            }

            $insertData[] = [
                'company_id' => $company_id,
                'category'   => 'akademik',
                'key'        => $key,
                'value'      => $mapAkademik[$key] ?? '',
                'mode'       => $mode,
                'persen'     => $persenAkademik[$key] ?? 0,
                'penilaian_type'  => $penilaian
            ];
        }

        foreach ($psikolog as $key) {
            $mode = $modePsikolog[$key] ?? null;

            $penilaian = 'angka';
            if ($mode === 'offline') {
                $penilaian = $penilaianPsikolog[$key] ?? null;
            }

            $insertData[] = [
                'company_id' => $company_id,
                'category'   => 'psikolog',
                'key'        => $key,
                'value'      => $mapPsikolog[$key] ?? '',
                'mode'       => $mode,
                'persen'     => $persenPsikolog[$key] ?? 0,
                'penilaian_type'  => $penilaian
            ];
        }

        if (!empty($insertData)) {
            $this->tryoutCabangModel->insertBatch($insertData);
        }

        return redirect()
            ->to(site_url('/maintenance/kategori-tryout'))
            ->with('success', 'Pengaturan tryout berhasil disimpan')
            ->with('selected_cabang', $company_id); // 🔥 ini penting
    }

    public function getByCabang()
    {
        $company_id = $this->request->getPost('cabang_id');

        if (!$company_id) {
            return $this->response->setJSON([
                'pilihan_akademik' => [],
                'pilihan_psikolog' => [],
                'akademik' => [],
                'psikolog' => [],
                'csrfHash' => csrf_hash()
            ]);
        }

        /*
        ================= MASTER PARAMETER
        */

        $pilihanAkademik = $this->parameter->getValue('akademik');
        $pilihanPsikolog = $this->parameter->getValue('psikolog');

        /*
        ================= DATA TERSIMPAN
        */

        $selected = $this->tryoutCabangModel
            ->where('company_id', $company_id)
            ->findAll();

        $map = [
            'akademik' => [],
            'psikolog' => []
        ];

        foreach ($selected as $row) {

            $map[$row['category']][$row['key']] = [
                'persen' => $row['persen'],
                'mode'   => $row['mode'],
                'penilaian_type'   => $row['penilaian_type']
            ];
        }

        /*
        ================= AKADEMIK
        */

        $akademik = [];

        foreach ($pilihanAkademik as $row) {

            $saved = $map['akademik'][$row['key']] ?? null;

            $akademik[] = [
                'key' => $row['key'],
                'value' => $row['value'],
                'persen' => $saved['persen'] ?? '',
                'mode' => $saved['mode'] ?? '',
                'penilaian_type' => $saved['penilaian_type'] ?? '',
                'checked' => $saved ? true : false
            ];
        }

        /*
        ================= PSIKOLOG
        */

        $psikolog = [];

        foreach ($pilihanPsikolog as $row) {

            $saved = $map['psikolog'][$row['key']] ?? null;

            $psikolog[] = [
                'key' => $row['key'],
                'value' => $row['value'],
                'persen' => $saved['persen'] ?? '',
                'mode' => $saved['mode'] ?? '',
                'penilaian_type' => $saved['penilaian_type'] ?? '',
                'checked' => $saved ? true : false
            ];
        }

        return $this->response->setJSON([
            'pilihan_akademik' => $pilihanAkademik,
            'pilihan_psikolog' => $pilihanPsikolog,
            'akademik' => $akademik,
            'psikolog' => $psikolog,
            'csrfHash' => csrf_hash()
        ]);
    }
}
