<?php

namespace App\Controllers;
use App\Controllers\BaseController;

class Help extends BaseController
{

    protected array $menuItems = [];

    public function __construct()
    {
        helper('auth');

        $this->menuItems = user_menu();
    }

    private function baseData(): array
    {
        $data = default_parser_item([]); // Pastikan helper ini tersedia
        $data['menuItems'] = $this->menuItems;
        return $data;
    }

    public function index()
    {
        return view('help/index', $this->baseData());
    }
}