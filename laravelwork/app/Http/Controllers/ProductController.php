<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use withFileUploads;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

            if($request->file('image')) {
                $original = $request->file('image')->getClientOriginalName();
                $image_name = date('Ymd_His') . '_' . $original;
        
                $request->file('image')->storeAs('public/product_images', $image_name);

                Product::query()->create([
                    'shop_id' => $request['shop_id'],
                    'name' => $request['name'], 
                    'price' => $request['price'], 
                    'stock' => $request['stock'], 
                    'discription' => $request['discription'],
                    'image' => $image_name,
                ]);
            } else {
                if( $shop && $shop_user_id === $auth ) {
                    $new_product = Product::query()->create([
                        'shop_id' => $request['shop_id'],
                        'name' => $request['name'], 
                        'price' => $request['price'], 
                        'stock' => $request['stock'], 
                        'discription' => $request['discription'],
                        'image' => $request['image'],
                    ]);
                } else {
                    return back()->with('product_register_err', 'エラーが発生しました。');
                }
            }

            return redirect(route('shop_detail', [$shop->id, $shop->name]))->with('product_register_success', '商品を登録しました。');
        } else {
            return back()->with('product_register_err', 'エラーが発生しました。');
        }

    }

    /**
     * 商品編集フォームの表示
     * 
     */
    public function productEditForm($id) {
        $product_info = Product::find($id);

        return view('product_edit_form', ['product_info' => $product_info]);

    }

    /**
     * 商品編集機能
     * 
     */
    public function productEdit(Request $request) {
        $auth = $request->login_user;
        $product_id = $request->product_id;
        $shop_id = $request->shop_id;
        $shop = Shop::find($shop_id);
        $shop_user_id = $shop->user_id;
        $shop_name = $shop->name;

        if( Auth::user()->id == $auth && $shop_user_id == $auth ) {

            if($request->file('image')) {
                $original = $request->file('image')->getClientOriginalName();
                $image_name = date('Ymd_His') . '_' . $original;
                $request->file('image')->storeAs('public/product_images', $image_name);

                Product::where('id', '=', $product_id)
                ->update([
                    'shop_id' => $request->shop_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'stock' => $request->stock,
                    'discription' => $request->discription,
                    'image' => $image_name,
                ]);
            } else {
                Product::where('id', '=', $product_id)
                ->update([
                    'shop_id' => $request->shop_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'stock' => $request->stock,
                    'discription' => $request->discription,
                    'image' => $request->image,
                ]);

            }

            return redirect(route('shop_detail',[$shop_id, $shop_name]))->with('product_edit_success', '商品情報を更新しました');

        } else {
            return back()->with('product_edit_err', 'エラーが発生しました。');
        }
    }

    /**
     * 商品削除機能
     * 
     */
    public function productDestroy(Request $request) {
        $original = $request->product_id;
        $product_id = intval($original);
        $product = Product::find($product_id);
        $login_user = $request->login_user;

        if( $login_user == Auth::user()->id ) {
            Product::where('id', $product_id)->delete();
            \Storage::disk('public')->delete('product_images', $product->image);
            return back()->with('product_delete_success', '商品を削除しました。');
        } else {
            return back()->with('product_delete_err', 'エラーが発生しました。');
        }


    }


}