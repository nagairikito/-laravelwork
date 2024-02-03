<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

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


/**
 * 認証(ログイン)前と後でアクセス権限を分ける(ミドルウェア)
 *  */ 

// // 認証(ログイン)前 app\Http\Middleware\Authenticate.phpを参照　
// Route::group(['middleware' => ['guest']], function() {

// });

// 認証(ログイン)後 app\Providers\RouteServiceProvider.phpを参照
Route::group(['middleware' => ['auth']], function() {
    // ログアウト
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // ショップオーナーアカウント
    Route::get('/shop_orner/{id}', [UserController::class, 'shopOrner'])->name('shop_orner');
});

// トップページ(ショップ一覧、商品一覧を表示)
Route::get('/', [HomeController::class, 'allList'])->name('home');

// 新規登録フォーム
Route::get('/register_form', [UserController::class, 'registerForm'])->name('register_form');

// 新規登録の処理
Route::post('/register', [UserController::class, 'register'])->name('register');

// ログインフォーム
Route::get('/login_form', [UserController::class, 'loginForm'])->name('login_form');

// ログインの処理
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login');

// ショップ詳細
Route::get('/shop/{id}/{name}', [ShopController::class, 'shopDetail'])->name('shopDetail');

// 商品詳細
Route::get('/product/{id}/{name}', [ProductController::class, 'productDetail'])->name('productDetail');



