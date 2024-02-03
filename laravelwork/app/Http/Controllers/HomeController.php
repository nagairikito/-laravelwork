<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;


class HomeController extends Controller
{
    /**
     * ショップ一覧、商品一覧を表示する
     * 
     * @return view
     */
    public function allList() {
        $shops = Shop::paginate(10); // データベースから全データを取得し、$shopsに格納
        $products = Product::paginate(10); 
        return view('home', ['shops' => $shops, 'products' => $products]); // 取得したデータをトップページに渡し、表示する
    }
}
