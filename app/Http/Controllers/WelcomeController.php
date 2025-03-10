<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'welcome'],
        ];

        $active_menu = 'dashboard'; 
        return view('welcome', ['breadcrumb' => $breadcrumb, 'active_menu' => $active_menu]);
    }

}
