<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


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
            $product_info = Product::select([
                'p.id',
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

        return view('shop', ['shop' => $shop, 'product_info' => $product_info]); // $shop(データベースから検索したショップ)をshop.blade.phpに渡し、表示する

    }
}
