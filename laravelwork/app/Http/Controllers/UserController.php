<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * 新規登録フォームを表示する
     * 
     * @return view
     */
    public function registerForm() {
        return view('register_form');
    }

    /**
     * 新規登録の処理
     * 
     * @return view
     */
    public function register(UserRegisterRequest $request) {
        \DB::beginTransaction();
        try {
            User::query()->create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollback();
            abort(500);
        }
        return view('registered');

    }


    /**
     * ログインフォームを表示する
     * 
     * @return view
     */
    public function loginForm() {
        return view('login_form');
    }

    /**
     * ログインの処理
     * 
     * @return view
     */
    public function login(LoginRequest $user_info) {
        $credntials = $user_info->only('email', 'password');

        if( !Auth::user() ) {
            if( Auth::attempt($credntials) ) {
                $user_info->session()->regenerate();

                $user_id = Auth::user()->id;

                // DBからお気に入り商品情報を取得
                $favorite_products_info = Product::select([
                    'p.id',
                    'p.name',
                    'p.price',
                    'p.image',
                ])
                ->from('products as p')
                ->join('favoriteproducts as fp', function($join) {
                    $join->on('p.id', '=', 'fp.product_id');
                })
                ->where('fp.user_id', '=', $user_id)
                ->get();
    
                if( count($favorite_products_info) > 0 && !session()->has('favorite_products') ) {
                    $product_keys = [];
                    $product_info = [];
    
                    foreach( $favorite_products_info as $favorite_product ) {
                        $product_keys[] = $favorite_product->id;
                        $product_info[] = $favorite_product;
    
                    }
    
                    $favorite_products = array_combine($product_keys, $product_info);

                    foreach( $favorite_products as $key => $value ) {
                        if( $value["image"] == null ) {
                                $favorite_products[$key]["image"] = "no_image_logo.png";
                        }
                    }
    
    
                    session(['favorite_products' => $favorite_products]);
    
                }
                    
                return redirect(route('home'))->with('login_success', 'ログインしました。');
            }

            return redirect(route('login_form'))->with('login_error', 'メールアドレスかパスワードが間違っています。');
    
        } elseif ( Auth::user() ) {
            Auth::logout();

            if( Auth::attempt($credntials) ) {
                $user_info->session()->regenerate();
                return redirect(route('home'))->with('login_success', 'アカウントを変更しました。');
            }

            return redirect(route('login_form'))->with('login_error', 'メールアドレスかパスワードが間違っています。');

        } else {
            return redirect(route('login_form'))->with('login_error', 'エラーが発生しました。。');    
        }
        
    }

    /**
     * ログアウトの処理
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate(); // セッションを削除
        $request->session()->regenerateToken(); // セッションの再作成

        return redirect(route('home'))->with('logout_msg', 'ログアウトしました。');
    }

    /**
     * ショップオーナーアカウント
     * 
     * @return view
     */
    public function shopOrner($id) {
        $auth = Auth::user()->id; // ここでセッションに保存されているユーザーのidを取得し$authに格納

        if( $id == $auth ) { // home.blade.phpからわたってきたユーザーid($id)とセッションに保存されているユーザーidを照合し、合っていたらユーザーがもつショップ一覧を表示、違っていたらエラーメッセージとともにトップページに返す
            $user_shops = User::select([
                'u.id as user_id',
                'u.name as user_name',
                'u.email',
                's.id as shop_id',
                's.name as shop_name',
            ])
            ->from('users as u')
            ->join('shops as s', function($join) {
                $join->on('u.id', '=', 's.user_id');
            })
            ->where('u.id', '=', $id)
            ->get();
        } else {
            return redirect(route('home'))->with('user_shop_error', 'エラーが発生しました。');
        }

        if( count($user_shops) == 0 ) { // ユーザーがショップを開設しているかの判定
            $result = false;
        } else {
            $result = true;
        }

        return view('shop_orner', ['id' => $id, 'user_shops' => $user_shops, 'result' => $result]);
    }



}
