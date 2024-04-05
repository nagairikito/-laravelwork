<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;



class HomeController extends Controller
{
    /**
     * ショップ一覧、商品一覧を表示する
     * 
     * @return view
     */
    public function allList() {
        // $shops = Shop::paginate(5, ['*'], 'shopPage') // データベースから全データを取得し、$shopsに格納
        //     ->appends(['productPage' => \Request::get('productPage')]); 

        // $products = Product::paginate(5, ['*'], 'productPage')
        //     ->appends(['shopPage' => \Request::get('shopPage')]);

        $shops = Shop::paginate(5);
        $products = Product::paginate(5);

        $popular_products = Product::orderByDesc('access_count')->paginate(5);


        //（仮）
        $categorys = [
            "スマートフォン",
            "ゲーム",
            "ノートPC・デスクトップ",
            "PC関連",
            "ファッション",
            "フード",
            "本",
            "調理器具",
            "スポーツ",
            "アウトドア",
            "",
        ];

        return view('home', ['shops' => $shops, 'products' => $products, 'popular_products' => $popular_products, 'categorys' => $categorys]); // 取得したデータをトップページに渡し、表示する
    }
}
