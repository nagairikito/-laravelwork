<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
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

        

        $db_categories = Category::all();

        $category_key = [];
        $category_value = [];
        foreach( $db_categories as $key => $value ) {
            foreach( $value as $key2 => $value2) {
                if($value2 == "id") {
                    $category_key[] = $value['id'];
                }
                if($value2 == "category") {
                    $category_value[] = $value['category'];
                }
            }
        }
        $categories = array_combine($category_key, $category_value);
        

        return view('home', ['shops' => $shops, 'products' => $products, 'popular_products' => $popular_products, 'categories' => $categories]); // 取得したデータをトップページに渡し、表示する
    }



}
