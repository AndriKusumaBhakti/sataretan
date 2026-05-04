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


        /* ================= POST DATA ================= */

        $akademik  = $this->request->getPost('akademik') ?? [];
        $psikolog  = $this->request->getPost('psikolog') ?? [];
        $skd  = $this->request->getPost('skd') ?? [];

        $persenAkademik    = $this->request->getPost('persen_akademik') ?? [];
        $modeAkademik      = $this->request->getPost('mode_akademik') ?? [];
        $penilaianAkademik = $this->request->getPost('penilaian_akademik') ?? [];
        $programAkademik   = $this->request->getPost('program_akademik') ?? [];

        $persenPsikolog    = $this->request->getPost('persen_psikolog') ?? [];
        $modePsikolog      = $this->request->getPost('mode_psikolog') ?? [];
        $penilaianPsikolog = $this->request->getPost('penilaian_psikolog') ?? [];
        $programPsikolog   = $this->request->getPost('program_psikolog') ?? [];

        $persenskd    = $this->request->getPost('persen_skd') ?? [];
        $modeskd      = $this->request->getPost('mode_skd') ?? [];
        $penilaianskd = $this->request->getPost('penilaian_skd') ?? [];
        $programskd   = $this->request->getPost('program_skd') ?? [];


        /* ================= DELETE DATA LAMA ================= */

        $this->tryoutCabangModel
            ->where('company_id', $company_id)
            ->delete();


        /* ================= PARAMETER MASTER ================= */

        $paramAkademik = $this->parameter->getValue('akademik');
        $paramPsikolog = $this->parameter->getValue('psikolog');
        $paramskd = $this->parameter->getValue('skd');

        $mapAkademik = [];
        foreach ($paramAkademik as $p) {
            $mapAkademik[$p['key']] = $p['value'];
        }

        $mapPsikolog = [];
        foreach ($paramPsikolog as $p) {
            $mapPsikolog[$p['key']] = $p['value'];
        }

        $mapskd = [];
        foreach ($paramskd as $p) {
            $mapskd[$p['key']] = $p['value'];
        }


        $insertData = [];


        /* ================= SIMPAN AKADEMIK ================= */

        foreach ($akademik as $key) {

            $mode = $modeAkademik[$key] ?? null;

            $penilaian = 'angka';

            if ($mode === 'offline') {
                $penilaian = $penilaianAkademik[$key] ?? 'angka';
            }

            $program = $programAkademik[$key] ?? [];

            $insertData[] = [
                'company_id'      => $company_id,
                'category'        => 'akademik',
                'key'             => $key,
                'value'           => $mapAkademik[$key] ?? '',
                'mode'            => $mode,
                'persen'          => $persenAkademik[$key] ?? 0,
                'penilaian_type'  => $penilaian,
                'program'         => !empty($program) ? json_encode($program) : null
            ];
        }


        /* ================= SIMPAN PSIKOLOG ================= */

        foreach ($psikolog as $key) {

            $mode = $modePsikolog[$key] ?? null;

            $penilaian = 'angka';

            if ($mode === 'offline') {
                $penilaian = $penilaianPsikolog[$key] ?? 'angka';
            }

            $program = $programPsikolog[$key] ?? [];

            $insertData[] = [
                'company_id'      => $company_id,
                'category'        => 'psikolog',
                'key'             => $key,
                'value'           => $mapPsikolog[$key] ?? '',
                'mode'            => $mode,
                'persen'          => $persenPsikolog[$key] ?? 0,
                'penilaian_type'  => $penilaian,
                'program'         => !empty($program) ? json_encode($program) : null
            ];
        }

        /* ================= SIMPAN SKD ================= */

        foreach ($skd as $key) {

            $mode = $modeskd[$key] ?? null;

            $penilaian = 'angka';

            if ($mode === 'offline') {
                $penilaian = $penilaianskd[$key] ?? 'angka';
            }

            $program = $programskd[$key] ?? [];

            $insertData[] = [
                'company_id'      => $company_id,
                'category'        => 'skd',
                'key'             => $key,
                'value'           => $mapskd[$key] ?? '',
                'mode'            => $mode,
                'persen'          => $persenskd[$key] ?? 0,
                'penilaian_type'  => $penilaian,
                'program'         => !empty($program) ? json_encode($program) : null
            ];
        }


        /* ================= INSERT DATA ================= */

        if (!empty($insertData)) {
            $this->tryoutCabangModel->insertBatch($insertData);
        }


        /* ================= REDIRECT ================= */

        return redirect()
            ->to(site_url('/maintenance/kategori-tryout'))
            ->with('success', 'Pengaturan tryout berhasil disimpan')
            ->with('selected_cabang', $company_id);
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
        $pilihanskd = $this->parameter->getValue('skd');

        /*
        ================= DATA TERSIMPAN
        */

        $selected = $this->tryoutCabangModel
            ->where('company_id', $company_id)
            ->findAll();

        $map = [
            'akademik' => [],
            'psikolog' => [],
            'skd' => []
        ];

        foreach ($selected as $row) {

            $map[$row['category']][$row['key']] = [
                'persen' => $row['persen'],
                'mode'   => $row['mode'],
                'penilaian_type'   => $row['penilaian_type'],
                'program' => $row['program'] ? json_decode($row['program'], true) : []
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
                'program' => $saved['program'] ?? [],
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
                'program' => $saved['program'] ?? [],
                'checked' => $saved ? true : false
            ];
        }

        $skd = [];

        foreach ($pilihanskd as $row) {

            $saved = $map['skd'][$row['key']] ?? null;

            $skd[] = [
                'key' => $row['key'],
                'value' => $row['value'],
                'persen' => $saved['persen'] ?? '',
                'mode' => $saved['mode'] ?? '',
                'penilaian_type' => $saved['penilaian_type'] ?? '',
                'program' => $saved['program'] ?? [],
                'checked' => $saved ? true : false
            ];
        }

        return $this->response->setJSON([
            'pilihan_akademik' => $pilihanAkademik,
            'pilihan_psikolog' => $pilihanPsikolog,
            'pilihan_skd' => $pilihanskd,
            'akademik' => $akademik,
            'psikolog' => $psikolog,
            'skd' => $skd,
            'csrfHash' => csrf_hash()
        ]);
    }
}
