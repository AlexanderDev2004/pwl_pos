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

        // $data = UserModel::all();
        // $userCount = UserModel::where('level_id', 2)->count();
        // // dd($user);
        // return view('user', [
        //     'data' => $data,
        //     'userCount' => $userCount
        // ]);

        $user = UserModel::firstOrNew(
            [
                'username' => 'manager33',
                'nama' => 'manager tiga tiga',
                'password' => Hash::make('12345'),
                'level_id' => 2
            ]
        );
        $user-> save();

        // $user = UserModel::updateOrCreate(
        //     ['username' => 'manager22'], // Cek apakah username ini sudah ada
        //     [
        //         'nama' => 'manager dua dua',
        //         'password' => Hash::make('12345'),
        //         'level_id' => 2
        //     ]
        // );



        // $user = UserModel::firstOrCreate([
        //     'username' => 'manager22',
        //     'nama' => 'manager dua dua',
        //     'password' => Hash::make('12345'),
        //     'level_id' => 2
        // ]);

        // $user = UserModel::firstOrCreate(
        //     ['username' => 'manager'],
        //     ['nama' => 'manager']
        // );
        return view('user', ['data' => UserModel::all()]);
    }
}
