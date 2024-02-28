<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;

class ProductController extends Controller
{
    /**
     * 商品詳細を表示
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

    /**
     * 商品購入画面を表示
     * 
     */
    public function purchaseForm($id) {
        $product = Product::find($id);
        return view('purchase_form', ['product' => $product]);
    }

    /**
     * 商品購入処理
     * 
     */
    public function purchase(Request $request) {
        $id = $request->id;
        $number_sold = $request->number_sold;
        $product = Product::find($id);
        $stock = $product->stock;

        if ($stock < $number_sold) {
            return back()->with('stock_error', '在庫がありません。');
        } else {
            $update_stock = $stock - $number_sold;
        }
        dd($update_stock);    

    }

}