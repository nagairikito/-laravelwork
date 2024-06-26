<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use withFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ShopRegisterRequest;
use App\Http\Requests\ShopEditRequest;


class ShopController extends Controller
{
    
    /**
     * ショップ詳細画面を表示する
     * 
     * @return view
     */
    public function shopDetail($id) {
        // ショップ詳細
        $shop = Shop::find($id); // データベースから一致するショップを検索して$shopに格納
        
        if (is_null($shop)) { // 一致するショップがなっかた場合の処理
            \Session::flash('shopDetail_err_msg', '一致する店舗が見つかりません。'); // ショップがなかったらセッションにエラーメッセージを格納
            return redirect(route('home')); // ホームにリダイレクトする（この時エラーメッセージはセッションに入っているのでエラーメッセージは渡す必要がない）
        }

        // ショップ商品一覧
            $product_info = Product::select([ // ショップ詳細のリンクを踏むと店舗詳細画面に遷移し、ショップが出品している商品一覧を表示する
                'p.id as product_id',
                'p.name as product_name',
                'p.price',
                'p.stock',
                'p.shop_id',
                's.name as shop_name'
            ])
            ->from('products as p')
            ->join('shops as s', function($join) {
                $join->on('p.shop_id', '=', 's.id');
            })
            ->where('p.shop_id', '=', $id)
            ->get();

            if( count($product_info) == 0 ) { // 商品の有無を表示(無の場合、商品情報がありませんと表示する)
                $result = false;
            } else {
                $result = true;
            }
    
    

        return view('shop', ['shop' => $shop, 'product_info' => $product_info, 'result' => $result]); // $shop(データベースから検索したショップ)、ショップ出品商品一覧、ショップ出品商品の有無をshop.blade.phpに渡し、表示する

    }

    /**
     * ショップ開設・登録フォームを表示する
     * 
     */
    public function shopRegisterForm() {
        return view('shop_register_form');

    }

    /**
     * ショップ登録処理
     * 
     */
    public function shopRegister(ShopRegisterRequest $request) {
        if( Auth::user() ) {

            if($request->file('image')) {
                $original = $request->file('image')->getClientOriginalName();
                $image_name = date('Ymd_His') . '_' . $original;
    
                $request->file('image')->storeAs('public/shop_images', $image_name);

                Shop::query()->create([
                    'user_id' => $request['user_id'],
                    'name' => $request['name'], 
                    'discription' => $request['discription'],
                    'image' => $image_name,
                ]);
            } else {
                Shop::query()->create([
                    'user_id' => $request['user_id'],
                    'name' => $request['name'], 
                    'discription' => $request['discription'],
                    'image' => $request['image'],
                ]);
            }

            return redirect(route('shop_orner', [Auth::user()->id]))->with('shop_register_success', 'ショップを登録しました。');

        } else {
            return redirect(route('shop_register_form'))->with('shop_register_err', 'ユーザー情報が見つかりません。');
        }


    }

    /**
     * ショップ編集フォームを表示する
     * 
     */
    public function shopEditForm($id) {
        // $id = intval($id);
        $shop_info = Shop::find($id);
        return view('shop_edit_form', ['shop' => $shop_info]);
    }

    /**
     * ショップ編集機能
     * 
     */
    public function shopEdit(Request $request) {
        $auth = $request->login_user;
        $shop_id = $request->shop_id;
        $shop_info = Shop::find($shop_id);
        $shop_user_id = $shop_info->user_id;

        if( Auth::user()->id == $auth && $shop_user_id == $auth ) {

            if ($request->file('image')) {
                $original = $request->file('image')->getClientOriginalName();
                $image_name = date('Ymd_His') . '_' . $original;
                $request->file('image')->storeAs('public/shop_images', $image_name);


                Shop::where('id', '=', $shop_id)
                ->update([
                    'user_id' => $request['login_user'],
                    'name' => $request['name'], 
                    'discription' => $request['discription'],
                    'image' => $image_name,
                ]);

                return redirect(route('shop_orner',[Auth::user()->id]))->with('shop_edit_success', 'ショップ情報を更新しました');
    
            } else {

                Shop::where('id', '=', $shop_id)
                ->update([
                    'user_id' => $request['login_user'],
                    'name' => $request['name'], 
                    'discription' => $request['discription'],
                    'image' => $request['image'],
                ]);
    
                    return redirect(route('shop_orner',[Auth::user()->id]))->with('shop_edit_success', 'ショップ情報を更新しました');
    
    
            }

        } else {
            return back()->with('shop_edit_err', 'エラーが発生しました。');
        }


    }



    /**
     * ショップ削除機能
     * 
     */
    public function shopDestroy(Request $request) {
        $auth = $request->login_user;
        $shop_id = $request->shop_id;

        if( $auth == Auth::user()->id ) {
    
            $shop = Shop::find($shop_id);

            if( $shop->image ) {
                \Storage::disk('public')->delete('shop_images/' . $shop->image);
            }


            $product_images_data = Product::where('shop_id', $shop_id)->get();
            $product_images = $product_images_data->pluck('image');
            foreach($product_images as $product_image) {
                \Storage::disk('public')->delete('product_images/' . $product_image);
            }


            Shop::where('id', $shop_id)->delete();
            Product::where('shop_id', $shop_id)->delete();


            return back()->with('shop_delete_success', 'ショップを削除しました。');

        } else {
            return back()->with('shop_delete_err', 'エラーが発生しました。');
        }

    }

}
