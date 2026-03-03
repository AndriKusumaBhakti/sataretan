<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserPaketModel;
use App\Models\TryoutModel;
use App\Models\PaketModel;
use App\Models\CompanyModel;
use App\Models\ParameterModel;
use Google\Client as GoogleClient;

class Auth extends BaseController
{
    protected array $menuItems = [];
    protected $userModel;
    protected $userPaketModel;
    protected $tryoutModel;
    protected $paketModel;
    protected $company;
    protected $parameter;

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
        $this->userModel = new UserModel();
        $this->userPaketModel = new UserPaketModel();
        $this->tryoutModel = new TryoutModel();
        $this->paketModel = new PaketModel();
        $this->company = new CompanyModel();
        $this->parameter = new ParameterModel();
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
            $data = $this->baseData();
            $data['grafik_bulanan_company'] = $this->tryoutModel->getGrafikBulananCompanyKategori();

            return view('index', $data);
        }
        $data = $this->baseData();
        $data['tryout'] = $this->tryoutModel->getStatistikGlobal(isGuruOrAdmin());
        $data['tryout_grafik'] = $this->tryoutModel->getGrafikPerKategori(false);
        $data['grafik_bulanan'] = $this->tryoutModel->getGrafikBulananKategori();
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
            'user_id'       => $user['id'],
            'name'          => $user['name'],
            'email'         => $user['email'],
            'phone'         => $user['phone'],
            'role'          => $user['role'],
            'companyid'    => $user['company_id'],
            'photo'         => $user['photo'],
            'paket'         => $paket,
            'logged'        => true
        ]);

        session()->set([
            'Authorization' => $token,
            'name' => $user['name'],
            'photo' => $user['photo'],
            'isLoggedIn'    => true
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

        $data['company'] = $this->company->where('status', 'active')->orderBy('id', 'ASC')
            ->findAll();


        $data['program'] = $this->parameter->getValue("program");

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
            'company_id' => 'required',
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
                'company_id'   => $this->request->getPost('company_id'),
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

    public function forgotPassword()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');

        $user = $this->userModel
            ->where('email', $email)
            ->first();

        // Jangan beri tahu apakah email terdaftar atau tidak
        if (!$user) {
            return redirect()->back()
                ->with('success', 'Email tidak ditemukan');
        }

        $token   = bin2hex(random_bytes(32));
        $expired = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->userModel->update($user['id'], [
            'reset_token'   => $token,
            'reset_expired' => $expired
        ]);

        $resetLink = base_url('reset-password/' . $token);

        $emailService = \Config\Services::email();

        $emailService->setFrom(env('email.fromEmail'), env('email.fromName'));
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password');
        $emailService->setMessage("
    <h3>Reset Password</h3>
    <p>Klik link berikut untuk reset password:</p>
    <a href='{$resetLink}'>{$resetLink}</a>
    <p>Link berlaku 1 jam.</p>
");

        if (!$emailService->send()) {
            dd($emailService->printDebugger(['headers']));
        }

        return redirect()->back()
            ->with('success', 'Link reset sudah dikirim ke email anda.');
    }

    public function formReset($token)
    {
        $user = $this->userModel
            ->where('reset_token', $token)
            ->where('reset_expired >=', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            return redirect()->to('/login')
                ->with('errors', ['Token tidak valid atau sudah kadaluarsa']);
        }

        return view('reset-password', [
            'token' => $token
        ]);
    }

    public function resetPassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');

        $user = $this->userModel
            ->where('reset_token', $token)
            ->where('reset_expired >=', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            return redirect()->to('/login')
                ->with('errors', ['Token tidak valid atau sudah kadaluarsa']);
        }

        $this->userModel->update($user['id'], [
            'password'       => md5($this->request->getPost('password')),
            'reset_token'    => null,
            'reset_expired'  => null,
            'updated_at'     => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/login')
            ->with('success', 'Password berhasil diperbarui, silakan login.');
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

        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(base_url('auth/googleCallback'));

        $client->addScope('email');
        $client->addScope('profile');

        $client->setAccessType('offline');
        $client->setPrompt('select_account');

        // Simpan state untuk keamanan (CSRF protection)
        $state = bin2hex(random_bytes(16));
        session()->set('oauth2state', $state);
        $client->setState($state);

        return redirect()->to($client->createAuthUrl());
    }

    public function googleCallback()
    {
        $client = new GoogleClient();

        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(site_url('auth/googleCallback'));

        // 🔐 Validasi state (WAJIB kalau sebelumnya diset)
        if ($this->request->getGet('state') !== session()->get('oauth2state')) {
            return redirect()->to('/login')->with('errors', 'Invalid OAuth state');
        }

        $code = $this->request->getGet('code');

        if (!$code) {
            return redirect()->to('/login')->with('errors', 'Authorization code tidak ditemukan');
        }

        $accessToken = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($accessToken['error'])) {
            return redirect()->to('/login')->with('errors', 'Gagal mengambil access token');
        }

        $client->setAccessToken($accessToken['access_token']);

        $googleService = new \Google\Service\Oauth2($client);
        $googleUser = $googleService->userinfo->get();

        $user = $this->userModel
            ->withRole()
            ->where('email', $googleUser->email)
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

        // 🎟 Generate JWT (jangan override access token Google)
        $jwtToken = generateJWT([
            'user_id'    => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'phone'      => $user['phone'],
            'role'       => $user['role'],
            'companyid'  => $user['company_id'],
            'photo'      => $user['photo'],
            'paket'      => $paket,
            'logged'     => true
        ]);

        session()->set([
            'Authorization' => $jwtToken,
            'name'          => $user['name'],
            'photo'         => $user['photo'],
            'isLoggedIn'    => true
        ]);

        last_time_activity_session('renew');

        return redirect()->to(site_url('/dashboard'));
    }
}
