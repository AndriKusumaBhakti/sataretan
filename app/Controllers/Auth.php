<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserPaketModel;
use App\Models\TryoutModel;
use App\Models\PaketModel;
use Google\Client as GoogleClient;

class Auth extends BaseController
{
    protected array $menuItems = [];
    protected $userModel;
    protected $userPaketModel;
    protected $tryoutModel;
    protected $paketModel;

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
        $this->userModel = new UserModel();
        $this->userPaketModel = new UserPaketModel();
        $this->tryoutModel = new TryoutModel();
        $this->paketModel = new PaketModel();
    }

    private function baseData(): array
    {
        $data = default_parser_item([]); // Pastikan helper ini tersedia
        $data['menuItems'] = $this->menuItems;
        return $data;
    }

    public function index()
    {
        if (empty(user_id())) {
            return view('index', $this->baseData());
        }
        $data = $this->baseData();
        $data['tryout'] = $this->tryoutModel->getStatistikGlobal(isGuruOrAdmin());
        $data['tryout_grafik'] = $this->tryoutModel->getGrafikPerKategori(false);
        return view('dashboard', $data);
    }

    public function masuk()
    {
        return view('login', $this->baseData());
    }

    public function login()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        // cek user
        if (empty($email) && empty($password)) {
            return redirect()->back()->with('errors', ['Email dan Password tidak boleh kosong']);
        } else if (empty($email)) {
            return redirect()->back()->with('errors', ['Email tidak boleh kosong']);
        } else if (empty($password)) {
            return redirect()->back()->with('errors', ['Password tidak boleh kosong']);
        }

        $user = $this->userModel
            ->withRole()
            ->where('email', $email)
            ->where('password', md5($password))
            ->first();

        // cek user
        if (!$user) {
            return redirect()->back()->with('errors', ['Email atau password salah']);
        }
        $paket = "";
        if (!validasiRole($user['role'])) {
            // Ambil paket aktif user
            $paket = $this->userPaketModel->getActivePaketUser($user['id']);
            if (!$paket) {
                return redirect()->back()->with('errors', ['Menunggu verifikasi admin']);
            }
        }

        // TODO: Tambahkan proses autentikasi (cek username/password)
        $token = generateJWT([
            'user_id' => $user['id'],
            'name'    => $user['name'],
            'email'    => $user['email'],
            'phone'    => $user['phone'],
            'role'    => $user['role'],
            'photo'   => $user['photo'],
            'paket'   => $paket,
            'logged'  => true
        ]);

        session()->set([
            'Authorization' => $token,
        ]);
        last_time_activity_session('renew');

        // Saat ini langsung redirect tanpa login, harusnya proses login dilakukan dulu
        return redirect()->to(site_url('/dashboard'));
    }

    public function register()
    {
        $data = $this->baseData();
        $data['paket'] = $this->paketModel->where('is_active', 1)->orderBy('id', 'ASC')
            ->findAll();

        return view('register', $data);
    }

    public function save()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'phone' => 'required|min_length[10]',
            'program' => 'required',
            'paket_id' => 'required',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // INSERT USER + AMBIL ID
            $userId = $this->userModel->insert([
                'name'   => $this->request->getPost('name'),
                'email'      => $this->request->getPost('email'),
                'phone'      => $this->request->getPost('phone'),
                'password'   => md5(
                    $this->request->getPost('password')
                ),
                'role_id'       => 3,
                'created_at' => date('Y-m-d H:i:s')
            ], true);
            if (!$userId) {
                $db->transRollback();
                return redirect()->back()->with('errors', $this->userModel->errors());
            }

            // INSERT USER_PAKET
            $this->userPaketModel->insert([
                'user_id'  => $userId,
                'program' => $this->request->getPost('program'),
                'paket_id' => $this->request->getPost('paket_id'),
                'status'   => 'P',
            ]);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Gagal menyimpan paket user']);
            }

            $db->transCommit();

            return redirect()->to('/login')
                ->with('success', 'Registrasi berhasil');
        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()->back()
                ->with('errors', ['Terjadi kesalahan sistem']);
        }
    }


    public function forgot()
    {
        return view('forgot-password', $this->baseData());
    }

    public function logout()
    {
        session()->remove(array_keys(session()->get()));
        session()->destroy();

        return redirect()->to(site_url('/'));
    }

    public function google()
    {
        $client = new GoogleClient();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(base_url('auth/googleCallback'));
        $client->addScope(['email', 'profile']);

        return redirect()->to($client->createAuthUrl());
    }

    public function googleCallback()
    {
        $client = new GoogleClient();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(base_url('auth/googleCallback'));
        
        if (!$this->request->getGet('code')) {
            return redirect()->to('/login');
        }

        $token = $client->fetchAccessTokenWithAuthCode(
            $this->request->getGet('code')
        );

        if (isset($token['error'])) {
            return redirect()->to('/login');
        }

        $client->setAccessToken($token['access_token']);
        $googleService = new \Google\Service\Oauth2($client);
        $googleUser = $googleService->userinfo->get();

        $userModel = new UserModel();

        $user = $userModel->where('email', $googleUser->email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }
        $paket = "";
        if (!validasiRole($user['role'])) {
            // Ambil paket aktif user
            $paket = $this->userPaketModel->getActivePaketUser($user['id']);
            if (!$paket) {
                return redirect()->back()->with('errors', ['Status user tidak aktif']);
            }
        }
        // TODO: Tambahkan proses autentikasi (cek username/password)
        $token = generateJWT([
            'user_id' => $user['id'],
            'name'    => $googleUser->name,
            'email'    => $googleUser->email,
            'phone'    => $user['phone'],
            'role'    => $user['role'],
            'photo'   => $user['photo'],
            'paket'   => $paket,
            'logged'  => true
        ]);

        session()->set([
            'Authorization' => $token,
        ]);
        last_time_activity_session('renew');

        return redirect()->to('/dashboard');
    }
}
