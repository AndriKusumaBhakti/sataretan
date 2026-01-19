<?php

use Config\Database;
use App\Models\MenuModel;

/**
 * Cek apakah aplikasi sudah terinstal
 */
function install_success(): bool
{
    return check_db_connection();
}

/**
 * Cek koneksi database
 */
function check_db_connection(): bool
{
    try {
        $db = Database::connect();
        if ($db->initialize()) {
            return true;
        }
        return false;
    } catch (\Throwable $e) {
        throw new Exception('Koneksi database gagal: ' . $e->getMessage());
    }
}

function default_parser_item($add_item = array())
{
    $return = array(
        'base_url'          => base_url(),
        'site_url'          => site_url(),
        'favicon_url'       => base_url('assets/images/favicon.ico'),
        'copyright'         => '© ' . date('Y') . ' <a href="#">Sataretan Akademi · All Rights Reserved</a>',
        'current_url'       => current_url(),
        'version'           => '1.0.0',
        'elapsed_time'      => time(),
        'base_url_theme'    => base_url_theme() . '/',
    );

    if (!empty($add_item) and is_array($add_item)) {
        $return = array_merge($return, $add_item);
    }

    return $return;
}

function get_active_theme()
{
    return 'default';
}

function base_url_theme($add_link = '')
{
    $active_theme = get_active_theme();
    return base_url('assets/themes/' . $active_theme . '/' . $add_link);
}

function load_comp_css($target_href = array())
{
    $return = '';
    foreach ($target_href as $value) {
        $return .= '<link type="text/css" href="' . $value . '" rel="stylesheet">' . PHP_EOL;
    }
    return $return;
}

function load_comp_js($target_src = array())
{
    $return = '';
    foreach ($target_src as $value) {
        $return .= '<script src="' . $value . '" type="text/javascript"></script>' . PHP_EOL;
    }
    return $return;
}

function is_login()
{
    if (!empty($_SESSION['login_' . APP_PREFIX])) {
        # yang ini untuk cek last_time_activity session
        if (!is_ajax()) {
            $last_time = last_time_activity_session('get');
            $minute = getenv('SESSION_TIMEOUT_MINUTE') ?: 5;
            $expired_time = "-{$minute} minute";

            if (!empty($last_time) and $last_time < strtotime($expired_time, time())) {
                $_SESSION['login_' . APP_PREFIX] = null;
                return false;
            } else {
                last_time_activity_session('renew');
                return true;
            }
        }

        return true;
    }

    return false;
}

/**
 * Method untuk mendapatkan data session last time activity
 * @param  string $act get|renew
 * @return integer
 */
function last_time_activity_session($act)
{
    switch ($act) {
        case 'get':
            return isset($_SESSION['login_' . APP_PREFIX]['last_time_activity']) ? $_SESSION['login_' . APP_PREFIX]['last_time_activity'] : "";
            break;

        case 'renew':
            $_SESSION['login_' . APP_PREFIX]['last_time_activity'] = time();
            break;
    }
}

function is_ajax()
{
    /* AJAX check  */
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    }
    return false;
}

function jwt_payload(): ?object
{
    $token = authorization();

    if (!$token) {
        return null;
    }

    try {
        return validateJWT($token);
    } catch (\Throwable $e) {
        return null;
    }
}

function authorization()
{
    return session('Authorization');
}

function user_id(): ?int
{
    $jwt = jwt_payload();
    return $jwt?->data->user_id ?? null;
}

function user_role(): ?string
{
    $jwt = jwt_payload();
    return $jwt?->data->role ?? null;
}

function user_paket(): ?string
{
    $jwt = jwt_payload();
    return $jwt?->data->paket ?? null;
}

function has_role(string $role): bool
{
    return user_role() === $role;
}

function isGuruOrAdmin(): bool
{
    return in_array(user_role(), ['guru', 'super_admin'], true);
}

function validasiRole($role): bool
{
    return in_array($role, ['guru', 'super_admin'], true);
}

function user_menu(): array
{
    $userId = user_id();
    $role   = user_role();

    if (!$userId || !$role) {
        return [];
    }

    $menuModel = new MenuModel();

    if (in_array($role, ['super_admin', 'guru'], true)) {
        $rows = $menuModel
            ->where('is_active', 1)
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->findAll();
    } else {
        $rows = $menuModel
            ->select('menu.*')
            ->join('paket_menu', 'paket_menu.menu_id = menu.id')
            ->join('user_paket', 'user_paket.paket_id = paket_menu.paket_id')
            ->where('user_paket.user_id', $userId)
            ->where('menu.is_active', 1)
            ->orderBy('menu.parent_id')
            ->orderBy('menu.sort_order')
            ->findAll();
    }

    $menu = [];

    foreach ($rows as $r) {
        if ($r['parent_id'] === null) {
            $menu[$r['id']] = [
                'title'   => $r['title'],
                'icon'    => $r['icon'],
                'segment' => $r['segment'],
                'url'     => $r['url'] ? base_url($r['url']) : null,
                'submenu' => []
            ];
        } elseif (isset($menu[$r['parent_id']])) {
            $menu[$r['parent_id']]['submenu'][] = [
                'title'      => $r['title'],
                'url'        => base_url($r['url']),
                'subsegment' => $r['segment']
            ];
        }
    }

    return array_values(array_filter($menu, fn($m) => !empty($m)));
}
