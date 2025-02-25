<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        UserModel::where('username', 'customer-1')->update(['nama' => 'Pelanggan Pertama']);
        return view('user', ['data' => UserModel::all()]);
    }
}
