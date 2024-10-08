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


// トップページ(ショップ一覧、商品一覧を表示)
Route::get('/', [HomeController::class, 'allList'])->name('home');

// 検索機能
Route::get('/search', [ProductController::class, 'search'])->name('search');

// カテゴリー一覧
Route::get('/category/{id}/{category}', [ProductController::class, 'category'])->name('category');

// 新規登録フォーム
Route::get('/register_form', [UserController::class, 'registerForm'])->name('register_form');

// 新規登録の処理
Route::post('/register', [UserController::class, 'register'])->name('register');

// ログインフォーム
Route::get('/login_form', [UserController::class, 'loginForm'])->name('login_form');

// ログインの処理
// Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login');

// ショップ詳細
Route::get('/shop/{id}/{name}', [ShopController::class, 'shopDetail'])->name('shop_detail');

// 商品詳細
Route::get('/product/{id}/{name}', [ProductController::class, 'productDetail'])->name('product_detail');


    /**
     * 認証(ログイン)前と後でアクセス権限を分ける(ミドルウェア)
     *  */ 

    // 認証(ログイン)前 app\Http\Middleware\Authenticate.phpを参照　
    // Route::group(['middleware' => ['guest']], function() {

    // });

    // 認証(ログイン)後 app\Providers\RouteServiceProvider.phpを参照
    Route::group(['middleware' => ['auth']], function() {

        // ログアウト
        Route::post('/logout', [UserController::class, 'logout'])->name('logout');

        // パスワードセキュリティー
        Route::post('/password_security', [UserController::class, 'passSecurity'])->name('pass_security');

        // ユーザー情報編集フォーム
        Route::get('/user_edit_form', [UserController::class, 'userEditForm'])->name('user_edit_form');

        // ユーザー情報編集
        Route::post('/user_edit_form', [UserController::class, 'userEdit'])->name('user_edit');

        // 別のアカウントでログイン
        // Route::post('/change_account', [UserController::class, 'change_account'])->name('change_account');

        // ショップオーナーアカウント
        Route::get('/shop_orner/{id}', [UserController::class, 'shopOrner'])->name('shop_orner');

        // お気に入り機能
            // お気に入り商品ページ表示 
            Route::get('/favorite_product/{id}', [ProductController::class, 'favoriteProduct'])->name('favorite_product');

            // お気に入り商品を追加
            Route::post('/add_favorite_product', [ProductController::class, 'addFavoriteProduct'])->name('add_favorite_product');

            // お気に入り商品の削除
            Route::post('/delete_favorite_product', [ProductController::class, 'deleteFavoriteProduct'])->name('delete_favorite_product');

            // お気に入り商品をすべて削除
            Route::post('/delete_all_favorite_product', [ProductController::class, 'deleteAllFavoriteProduct'])->name('delete_all_favorite_product');

        // ショッピングカート機能
            // ショッピングカート表示
            Route::get('/shopping_cart/{id}/{name}', [ProductController::class, 'shoppingCart'])->name('shopping_cart');

            // ショッピングカートに商品を追加
            Route::post('/add_shopping_cart', [ProductController::class, 'addShoppingCart'])->name('add_shopping_cart');

            // ショッピングカートの商品を削除
            Route::post('/delete_shopping_cart', [ProductController::class, 'deleteShoppingCart'])->name('delete_shopping_cart');

            // ショッピングカートの商品をすべて削除
            Route::post('/delete_all_shopping_cart', [ProductController::class, 'deleteAllShoppingCart'])->name('delete_all_shopping_cart');

        // 商品購入画面の表示
        Route::get('/purchase_form/{id}/{name}', [ProductController::class, 'purchaseForm'])->name('purchase_form');

        // 商品の購入処理
        Route::post('/purchase', [ProductController::class, 'purchase'])->name('purchase');

        // 購入履歴
        Route::get('/purchased_product/{id}/{name}', [ProductController::class, 'purchasedProduct'])->name('purchased_product');

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

        // CSV出力
        Route::post('/csv_file', [ProductController::class, 'CSVFile'])->name('csv_file');

        // 商品の在庫数追加
        Route::post('/product_stock_add', [ProductController::class, 'productStockAdd'])->name('product_stock_add');

        // 商品編集フォームの表示
        Route::get('/product_edit_form/{id}/{name}', [ProductController::class, 'productEditForm'])->name('product_edit_form');

        // 商品の編集
        Route::post('/product_edit', [ProductController::class, 'productEdit'])->name('product_edit');

        // 商品の削除
        Route::post('/product_destroy', [ProductController::class, 'productDestroy'])->name('product_destroy');

    });

