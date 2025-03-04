<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // $data = [
        //     'level_id' => 2,
        //     'username' => 'manager_tiga',
        //     'nama' => 'Manager 3',
        //     'password' => Hash::make('12345')
        // ];
        // UserModel::create($data);


        // $user = UserModel::find(1);
        // $user = UserModel::where('user_id', 1)->first();
        // $user = UserModel::firstWhere('user_id', 1);
        // $user = UserModel::FindOr(20, ['username','nama'], function () {
        //     abort(404);
        // });
        // $user = UserModel::findOrfail(1);
        // $user = UserModel::where('username', 'manager9')->firstOrFail();
        $data = UserModel::all();
        $userCount = UserModel::where('level_id', 2)->count();
        // dd($user);
        return view('user', [
            'data' => $data,
            'userCount' => $userCount
        ]);
        return view('user', ['data' => UserModel::all()]);
    }
}
