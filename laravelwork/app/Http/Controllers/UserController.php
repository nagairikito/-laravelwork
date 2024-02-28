<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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
    public function register(RegisterRequest $request) {
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

        if (Auth::attempt($credntials)) {
            $user_info->session()->regenerate();
            return redirect(route('home'))->with('login_success', 'ログインしました。');
        }
        return redirect(route('login_form'))->with('login_error', 'メールアドレスかパスワードが間違っています。');
        
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
