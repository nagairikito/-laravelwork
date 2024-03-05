<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRegisterRequest;


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
        // dd($update_stock);    



            // Product::update('products')
            // ->set('stock', '=', $update_stock)
            // ->where('id', '=', $id)
            // ->save();

            \DB::beginTransaction();
            try {
                \DB::table('products')
                ->where('id', $id)
                ->update([
                    'stock' => $update_stock,
                ]);

                \DB::commit();
                return view('purchased');

            } catch(\Throwable $e) {
                \DB::rollback();
                abort(500);
            }


        }

    }

    /**
     * 商品登録フォームを表示する
     * 
     */
    public function productRegisterForm($shop_id) {
        $int_shop_id = intval($shop_id);
        return view('product_register_form', ['shop_id' => $int_shop_id]);
    }

    /**
     * 商品登録処理
     * 
     */
    public function productRegister(ProductRegisterRequest $request) {
        $shop_id = $request->shop_id;
        $shop = Shop::find($shop_id);
        $shop_user_id = $shop->user_id;
        $auth = Auth::user()->id;

        if( $shop && $shop_user_id === $auth ) {
            $new_product = Product::query()->create([
                'shop_id' => $request['shop_id'],
                'name' => $request['name'], 
                'price' => $request['price'], 
                'stock' => $request['stock'], 
                'discription' => $request['discription'],
            ]);
            return view('registered');
        } else {
            return back()->with('register_product_err', 'エラーが発生しました。');
        }
    }


}