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

    // ショップ開設フォーム
    Route::get('/shop_register_form', [ShopController::class, 'shopRegisterForm'])->name('shop_register_form');

    // ショップ登録
    Route::post('/shop_register', [ShopController::class, 'shopRegister'])->name('shop_register');

    // ショップ編集フォームの表示
    Route::get('/shop_edit_form/{id}/{name}', [ShopController::class, 'shopEditForm'])->name('shop_edit_form');

    // ショップの編集
    // Route::get('/shop_edit', [ShopController::class, 'shopEdit'])->name('shop_edit');
    Route::post('/shop_edit', [ShopController::class, 'shopEdit'])->name('shop_edit');

    // ショップの削除
    Route::post('/shop_destroy', [ShopController::class, 'shopDestroy'])->name('shop_destroy');

    // 商品登録フォーム
    Route::get('/product_register_form/{shop_id}', [ProductController::class, 'productRegisterForm'])->name('product_register_form');

    // 商品登録
    Route::post('/product_register', [ProductController::class, 'productRegister'])->name('product_register');

    // 商品の在庫数追加
    Route::post('/product_stock_add', [ProductController::class, 'productStockAdd'])->name('product_stock_add');

    // 商品編集フォームの表示
    Route::get('/product_edit_form/{id}/{name}', [ProductController::class, 'productEditForm'])->name('product_edit_form');

    // 商品の編集
    Route::post('/product_edit', [ProductController::class, 'productEdit'])->name('product_edit');

    // 商品の削除
    Route::post('/product_destroy', [ProductController::class, 'productDestroy'])->name('product_destroy');

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
Route::get('/shop/{id}/{name}', [ShopController::class, 'shopDetail'])->name('shop_detail');

// 商品詳細
Route::get('/product/{id}/{name}', [ProductController::class, 'productDetail'])->name('product_detail');

// 商品購入画面の表示
Route::get('/purchase_form/{id}/{name}', [ProductController::class, 'purchaseForm'])->name('purchase_form');

// 商品の購入処理
Route::post('/purchase', [ProductController::class, 'purchase'])->name('purchase');




