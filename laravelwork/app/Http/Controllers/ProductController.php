<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;

class ProductController extends Controller
{
    /**
     * 商品一覧を表示
     * 
     * @return view
     */
    public function productDetail($id) {
        $product = Product::find($id);
        
        if ( is_null($product) ) {
            \Session::flash('productDetail_err_msg', '商品情報がありません。');
            return redirect(route('home'));
        }

        $shop_id = $product->shop_id;
        $shop_name = Shop::find($shop_id)->name;

        return view('product', ['product' => $product, 'shop_name' => $shop_name]);

    }
}
