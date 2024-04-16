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
        $shops = Shop::Paginate(5, ['*'], 'shopsPage') // データベースから全データを取得し、$shopsに格納
            ->appends(['popularProductsPage' => \Request::get('popularProductsPage')]); 

        $products = Product::Paginate(5, ['*'], 'productsPage')
            ->appends(['shopsPage' => \Request::get('shopsPage')]);


        $popular_products = Product::orderByDesc('access_count')->Paginate(5, ['*'], 'popularProductsPage')
            ->appends(['productsPage' => \Request::get('productsPage')]);

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
