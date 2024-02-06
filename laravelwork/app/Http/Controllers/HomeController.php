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
        $shops = Shop::paginate(10, ['*'], 'shopPage') // データベースから全データを取得し、$shopsに格納
            ->appends(['productPage' => \Request::get('productPage')]); 

        $products = Product::paginate(10, ['*'], 'productPage')
            ->appends(['shopPage' => \Request::get('shopPage')]);

        return view('home', ['shops' => $shops, 'products' => $products]); // 取得したデータをトップページに渡し、表示する
    }
}
