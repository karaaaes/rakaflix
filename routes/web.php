<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Member\RegisterController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\MovieController as MemberMovieController;
use App\Http\Controllers\Member\LoginController as MemberLoginController;
use App\Http\Controllers\Member\PricingController;
use App\Http\Controllers\Member\TransactionController as MemberTransactionController;
use App\Http\Controllers\Member\UserPremiumController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
})->name('member.index');

Route::get('/admin', function () {
    return view('admin.dashboard');
});

Route::view('/payment-finish', 'member.payment-finish')->name('member.payment.finish');

Route::get('/admin/login', [LoginController::class, 'index'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'authenticate'])->name('admin.login.auth');

Route::get('/register', [RegisterController::class, 'index'])->name('member.register');
Route::post('/register', [RegisterController::class, 'store'])->name('member.register.store');

Route::get('/login', [MemberLoginController::class, 'index'])->name('member.login');
Route::post('/login', [MemberLoginController::class, 'auth'])->name('member.login.auth');

Route::get('/pricing', [PricingController::class, 'index'])->name('member.pricing');

//Nama Middleware ada di Kernel.php
Route::group(['prefix' => 'admin', 'middleware' => ['admin.auth']],function(){
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');

    Route::get('/logout',[LoginController::class, 'logout'])->name('admin.logout');

    //URL : baseurl/admin/movie
    Route::group(['prefix' => 'movie'],function(){
        Route::get('/', [MovieController::class, 'index'])->name('admin.movie');
        Route::get('/create', [MovieController::class, 'create'])->name('admin.movie-create');
        Route::post('/store', [MovieController::class, 'store'])->name('admin.movie-store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('admin.movie-edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('admin.movie-update');
        Route::delete('/delete/{id}', [MovieController::class, 'delete'])->name('admin.movie-delete');
    });

    Route::group(['prefix' => 'transaction'],function(){
        Route::get('/', [TransactionController::class, 'index'])->name('admin.transactions');
    });
});

Route::group(['prefix' => 'member', 'middleware' => ['auth']],function(){
    Route::get('/', [DashboardController::class, 'index'])->name('member.dashboard');
    Route::post('/logout', [MemberLoginController::class, 'logout'])->name('member.logout');
    Route::post('/transaction', [MemberTransactionController::class, 'store'])->name('member.transaction');
    Route::get('/movie/{id}', [MemberMovieController::class, 'show'])->name('member.movie.detail');
    Route::get('/movie/{id}/watch', [MemberMovieController::class, 'watch'])->name('member.movie.watching');
    Route::get('/subscription', [UserPremiumController::class, 'index'])->name('member.subscription');
    Route::delete('/subscription/{id}', [UserPremiumController::class, 'destroy'])->name('member.subscription.destroy');
});
