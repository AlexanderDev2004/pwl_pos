<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Models\Supplier;
use Dom\Attr;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

$router->pattern('id', '[0-9]+');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postregister'])->name('postregister');
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin'])->name('postlogin');
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [WelcomeController::class, 'index']);
    Route::get('/profile/{id}', [AuthController::class, 'profile'])->name('profile');
    Route::get('/profile/edit/{id}', [AuthController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [AuthController::class, 'update'])->name('profile.update');

    Route::middleware(['authorize:ADM,MNG'])->group(function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/create_ajax', [UserController::class, 'create_ajax'])->name('user.create_ajax');
            Route::post('/store_ajax', [UserController::class, 'store_ajax'])->name('user.store_ajax');
            Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax'])->name('user.show_ajax');
            Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax'])->name('user.edit_ajax');
            Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax'])->name('user.update_ajax');
            Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax'])->name('user.confirm_ajax');
            Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax'])->name('user.delete_ajax');
            Route::get('/import', [UserController::class, 'import'])->name('user.import');
            Route::post('/import_ajax', [UserController::class, 'import_ajax'])->name('user.import_ajax');
            Route::get('export_excel', [UserController::class, 'export_excel'])->name('user.export_excel');
            Route::get('export_pdf', [UserController::class, 'export_pdf'])->name('user.export_pdf');
            //====
            Route::get('/', [UserController::class, 'index'])->name('user');
            Route::post('/list', [UserController::class, 'list'])->name('user.list');
            Route::get('/create', [UserController::class, 'create'])->name('user.create');
            Route::post('/store', [UserController::class, 'store'])->name('user.store');
            Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy');
        });
    });
    Route::middleware(['authorize:ADM,MNG'])->group(function () {
        Route::group(['prefix' => 'level'], function () {
            Route::get('/create_ajax', [LevelController::class, 'create_ajax'])->name('level.create_ajax');
            Route::post('/store_ajax', [LevelController::class, 'store_ajax'])->name('level.store_ajax');
            Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax'])->name('level.show_ajax');
            Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax'])->name('level.edit_ajax');
            Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax'])->name('level.update_ajax');
            Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax'])->name('level.confirm_ajax');
            Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax'])->name('level.delete_ajax');
            //====
            Route::get('/', [LevelController::class, 'index'])->name('level');
            Route::post('/list', [LevelController::class, 'list'])->name('level.list');
            Route::get('/create', [LevelController::class, 'create'])->name('level.create');
            Route::post('/store', [LevelController::class, 'store'])->name('level.store');
            Route::get('/{id}', [LevelController::class, 'show'])->name('level.show');
            Route::get('/{id}/edit', [LevelController::class, 'edit'])->name('level.edit');
            Route::put('/{id}', [LevelController::class, 'update'])->name('level.update');
            Route::delete('/{id}', [LevelController::class, 'destroy'])->name('level.destroy');
        });
    });

    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'kategori'], function () {
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax'])->name('kategori.create_ajax');
            Route::post('/store_ajax', [KategoriController::class, 'store_ajax'])->name('kategori.store_ajax');
            Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax'])->name('kategori.show_ajax');
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax'])->name('kategori.edit_ajax');
            Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax'])->name('kategori.update_ajax');
            Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax'])->name('kategori.confirm_ajax');
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax'])->name('kategori.delete_ajax');
            //====
            Route::get('/', [KategoriController::class, 'index'])->name('kategori');
            Route::post('/list', [KategoriController::class, 'list'])->name('kategori.list');
            Route::get('/create', [KategoriController::class, 'create'])->name('kategori.create');
            Route::post('/store', [KategoriController::class, 'store'])->name('kategori.store');
            Route::get('/{id}', [KategoriController::class, 'show'])->name('kategori.show');
            Route::get('/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
            Route::put('/{id}', [KategoriController::class, 'update'])->name('kategori.update');
            Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
        });
    });

    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'supplier'], function () {
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax'])->name('supplier.create_ajax');
            Route::post('/store_ajax', [SupplierController::class, 'store_ajax'])->name('supplier.store_ajax');
            Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax'])->name('supplier.show_ajax');
            Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax'])->name('supplier.edit_ajax');
            Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax'])->name('supplier.update_ajax');
            Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax'])->name('supplier.confirm_ajax');
            Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax'])->name('supplier.delete_ajax');
            Route::get('/import', [SupplierController::class, 'import'])->name('supplier.import');
            Route::post('/import_ajax', [SupplierController::class, 'import_ajax'])->name('supplier.import_ajax');
            Route::get('export_excel', [SupplierController::class, 'export_excel'])->name('supplier.export_excel');
            Route::get('export_pdf', [SupplierController::class, 'export_pdf'])->name('supplier.export_pdf');
            //====
            Route::get('/', [SupplierController::class, 'index'])->name('supplier');
            Route::post('/list', [SupplierController::class, 'list'])->name('supplier.list');
            Route::get('/create', [SupplierController::class, 'create'])->name('supplier.create');
            Route::post('/store', [SupplierController::class, 'store'])->name('supplier.store');
            Route::get('/{id}', [SupplierController::class, 'show'])->name('supplier.show');
            Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
            Route::put('/{id}', [SupplierController::class, 'update'])->name('supplier.update');
            Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
        });
    });
    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'barang'], function () {
            Route::get('/create_ajax', [BarangController::class, 'create_ajax'])->name('barang.create_ajax');
            Route::post('/store_ajax', [BarangController::class, 'store_ajax'])->name('barang.store_ajax');
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax'])->name('barang.show_ajax');
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax'])->name('barang.edit_ajax');
            Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax'])->name('barang.update_ajax');
            Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax'])->name('barang.confirm_ajax');
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax'])->name('barang.delete_ajax');
            Route::get('/import', [BarangController::class, 'import'])->name('barang.import');
            Route::post('/import_ajax', [BarangController::class, 'import_ajax'])->name('barang.import_ajax');
            Route::get('export_excel', [BarangController::class, 'export_excel'])->name('barang.export_excel');
            Route::get('export_pdf', [BarangController::class, 'export_pdf'])->name('barang.export_pdf');
            //====
            Route::get('/', [BarangController::class, 'index'])->name('barang');
            Route::post('/list', [BarangController::class, 'list'])->name('barang.list');
            Route::get('/create', [BarangController::class, 'create'])->name('barang.create');
            Route::post('/store', [BarangController::class, 'store'])->name('barang.store');
            Route::get('/{id}', [BarangController::class, 'show'])->name('barang.show');
            Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
            Route::put('/{id}', [BarangController::class, 'update'])->name('barang.update');
            Route::delete('/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
        });
    });
    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'stok'], function () {
            Route::get('/create_ajax', [StokController::class, 'create_ajax'])->name('barang.create_ajax');
            Route::post('/store_ajax', [StokController::class, 'store_ajax'])->name('barang.store_ajax');
            Route::get('/{id}/show_ajax', [StokController::class, 'show_ajax'])->name('barang.show_ajax');
            Route::get('/{id}/edit_ajax', [StokController::class, 'edit_ajax'])->name('barang.edit_ajax');
            Route::put('/{id}/update_ajax', [StokController::class, 'update_ajax'])->name('barang.update_ajax');
            Route::get('/{id}/delete_ajax', [StokController::class, 'confirm_ajax'])->name('barang.confirm_ajax');
            Route::delete('/{id}/delete_ajax', [StokController::class, 'delete_ajax'])->name('barang.delete_ajax');
            Route::get('/import', [StokController::class, 'import'])->name('barang.import');
            Route::post('/import_ajax', [StokController::class, 'import_ajax'])->name('barang.import_ajax');
            Route::get('export_excel', [StokController::class, 'export_excel'])->name('barang.export_excel');
            Route::get('export_pdf', [StokController::class, 'export_pdf'])->name('barang.export_pdf');
            //====
            Route::get('/', [StokController::class, 'index'])->name('barang');
            Route::post('/list', [StokController::class, 'list'])->name('barang.list');
            Route::get('/create', [StokController::class, 'create'])->name('barang.create');
            Route::post('/store', [StokController::class, 'store'])->name('barang.store');
            Route::get('/{id}', [StokController::class, 'show'])->name('barang.show');
            Route::get('/{id}/edit', [StokController::class, 'edit'])->name('barang.edit');
            Route::put('/{id}', [StokController::class, 'update'])->name('barang.update');
            Route::delete('/{id}', [StokController::class, 'destroy'])->name('barang.destroy');
        });
    });
    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'penjualan'], function () {
            Route::get('/create_ajax', [PenjualanController::class, 'create_ajax'])->name('penjualan.create_ajax');
            Route::post('/store_ajax', [PenjualanController::class, 'store_ajax'])->name('penjualan.store_ajax');
            Route::get('/{id}/show_ajax', [PenjualanController::class, 'show_ajax'])->name('penjualan.show_ajax');
            Route::get('/{id}/edit_ajax', [PenjualanController::class, 'edit_ajax'])->name('penjualan.edit_ajax');
            Route::put('/{id}/update_ajax', [PenjualanController::class, 'update_ajax'])->name('penjualan.update_ajax');
            Route::get('/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax'])->name('penjualan.confirm_ajax');
            Route::delete('/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax'])->name('penjualan.delete_ajax');
            Route::get('/import', [PenjualanController::class, 'import'])->name('penjualan.import');
            Route::post('/import_ajax', [PenjualanController::class, 'import_ajax'])->name('penjualan.import_ajax');
            Route::get('export_excel', [PenjualanController::class, 'export_excel'])->name('penjualan.export_excel');
            Route::get('export_pdf', [PenjualanController::class, 'export_pdf'])->name('penjualan.export_pdf');
            //====
            Route::get('/', [PenjualanController::class, 'index'])->name('penjualan');
            Route::post('/list', [PenjualanController::class, 'list'])->name('penjualan.list');
            Route::get('/create', [PenjualanController::class, 'create'])->name('penjualan.create');
            Route::post('/store', [PenjualanController::class, 'store'])->name('penjualan.store');
            Route::get('/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
            Route::get('/{id}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
            Route::put('/{id}', [PenjualanController::class, 'update'])->name('penjualan.update');
            Route::delete('/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
        });
    });
});
