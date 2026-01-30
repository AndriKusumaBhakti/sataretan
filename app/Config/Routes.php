<?php

use CodeIgniter\Router\RouteCollection;

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

$routes->get('/', 'Auth::index');
$routes->get('dashboard', 'Auth::index');
$routes->get('login', 'Auth::masuk');
$routes->post('login', 'Auth::login');
$routes->get('auth/google', 'Auth::google');
$routes->get('auth/googleCallback', 'Auth::googleCallback');
$routes->get('logout', 'Auth::logout');

$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::save');
$routes->get('forgot-password', 'Auth::forgot');
$routes->get('file/(:segment)/(:any)', 'FileController::show/$1/$2');
$routes->get('file-sub/(:segment)/(:any)', 'FileController::showSub/$1/$2');
$routes->group('/', ['filter' => 'jwt'], function ($routes) {
    $routes->get('profile', 'Profile::index');
    $routes->get('profile/edit', 'Profile::edit');
    $routes->post('profile/update', 'Profile::update');
    $routes->get('help', 'Help::index');
    $routes->get('account-settings', 'Profile::accountSettings');
    $routes->post('change-password', 'Profile::changePassword');

    $routes->get('settings', 'Profile::settings'); // optional
    $routes->get('logout', 'Auth::logout');

    /* =====================
     *  MATERI (GURU & SISWA)
     * ===================== */
    $routes->group('materi', ['filter' => 'role:super_admin,admin,guru,siswa'], function ($routes) {
        $routes->get('(:segment)', 'Materi::index/$1');
        $routes->get('(:segment)/create', 'Materi::create/$1');
        $routes->post('store', 'Materi::store');

        $routes->get('(:segment)/(:segment)/view/(:num)', 'Materi::view/$1/$2/$3');
        $routes->get('(:segment)/edit/(:num)', 'Materi::edit/$1/$2');
        $routes->post('update/(:num)', 'Materi::update/$1');

        $routes->get('delete/(:num)', 'Materi::delete/$1');
    });

    /* =====================
     *  TRYOUT (SISWA ONLY)
     * ===================== */
    $routes->group('tryout', ['filter' => 'role:super_admin,admin,guru,siswa'], function ($routes) {
        $routes->get('(:segment)', 'Tryout::index/$1');
        $routes->get('(:segment)/start/(:num)', 'Tryout::start/$1/$2');
        $routes->get('(:segment)/pengerjaan/(:num)/(:num)', 'Tryout::pengerjaan/$1/$2/$3');
        $routes->get('(:segment)/submit/(:num)', 'Tryout::submit/$1/$2');
        $routes->get('(:segment)/hasil/(:num)', 'Tryout::hasil/$1/$2');
        $routes->post('save-jawaban', 'Tryout::saveJawaban');

        //admin or guru
        $routes->get('(:segment)/tambah', 'Tryout::tambah/$1');
        $routes->post('(:segment)/simpan', 'Tryout::simpan/$1');
        $routes->post('(:segment)/publish/(:num)', 'Tryout::publish/$1/$2');
        $routes->post('(:segment)/unpublish/(:num)', 'Tryout::unpublish/$1/$2');
        $routes->get('(:segment)/edit/(:num)', 'Tryout::edit/$1/$2');
        $routes->post('(:segment)/update/(:num)', 'Tryout::update/$1/$2');
        $routes->post('(:segment)/delete/(:num)', 'Tryout::delete/$1/$2');

        //tambah soal
        $routes->get('(:segment)/(:num)/soal', 'TryoutSoal::index/$1/$2');
        $routes->get('(:segment)/(:num)/soal/tambah', 'TryoutSoal::tambah/$1/$2');
        $routes->post('(:segment)/(:num)/soal/simpan', 'TryoutSoal::simpan/$1/$2');
        $routes->get('(:segment)/(:num)/soal/edit/(:num)', 'TryoutSoal::edit/$1/$2/$3');
        $routes->post('(:segment)/(:num)/soal/update/(:num)', 'TryoutSoal::update/$1/$2/$3');
        $routes->get('(:segment)/(:num)/soal/hapus/(:num)', 'TryoutSoal::hapus/$1/$2/$3');
        $routes->post('(:segment)/(:num)/soal/upload-excel', 'TryoutSoal::uploadExcel/$1/$2');

        //jasmani
        $routes->get('(:segment)/view', 'Jasmani::index/$1');
        $routes->get('(:segment)/create', 'Jasmani::create/$1');
        $routes->post('(:segment)/store', 'Jasmani::store/$1');
        $routes->get('(:segment)/detail/(:num)', 'Jasmani::detail/$1/$2');
        $routes->get('(:segment)/remove/(:num)', 'Jasmani::delete/$1/$2');

        $routes->get('(:segment)/nilai/(:num)', 'TryoutNilai::nilai/$1/$2');
        $routes->get('(:segment)/nilai/reset/(:num)', 'TryoutNilai::reset/$1/$2');
        $routes->get('(:segment)/nilai/detail/(:num)', 'TryoutNilai::detail/$1/$2');
        $routes->get('(:segment)/nilai/export-pdf/(:num)', 'TryoutNilai::exportPdf/$1/$2');
        $routes->get('(:segment)/nilai/export-excel/(:num)', 'TryoutNilai::exportExcel/$1/$2');
    });

    $routes->group('video', ['filter' => 'role:super_admin,admin,guru,siswa'], function ($routes) {
        $routes->get('(:segment)', 'Video::index/$1');
        $routes->get('(:segment)/create', 'Video::create/$1');
        $routes->post('store', 'Video::store');

        $routes->get('(:segment)/(:segment)/view/(:num)', 'Video::view/$1/$2/$3');
        $routes->get('(:segment)/edit/(:num)', 'Video::edit/$1/$2');
        $routes->post('update/(:num)', 'Video::update/$1');

        $routes->get('delete/(:num)', 'Video::delete/$1');
    });

    $routes->group('master-data', ['filter' => 'role:super_admin,admin,guru,siswa'], function ($routes) {
        $routes->get('(:segment)', 'User::index/$1');
        $routes->post('(:segment)/approve/(:num)', 'User::approve/$1/$2');
        $routes->get('(:segment)/create', 'User::create/$1');
        $routes->post('(:segment)/store', 'User::store/$1');
        $routes->get('(:segment)/edit/(:num)', 'User::edit/$1/$2');
        $routes->post('(:segment)/update/(:num)', 'User::update/$1/$2');
        $routes->post('(:segment)/delete/(:num)', 'User::delete/$1/$2');

        $routes->get('(:segment)/export-excel', 'TryoutNilai::exportExcelRekap/$1');
    });

    $routes->group('kalkulator', ['filter' => 'role:super_admin,admin,guru,siswa'], function ($routes) {
        $routes->post('bmi', 'Kalkulator::hitungBmi');
        $routes->post('hitung', 'Kalkulator::hitung');
    });

    $routes->get('uu/kepolisian', 'Materi::uu', ['filter' => 'role:super_admin,guru,siswa']);
});
