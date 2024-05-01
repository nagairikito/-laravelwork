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

        $update_access_count = $product->access_count + 1;
        Product::where('id', '=', $id)->update(['access_count' => $update_access_count]);

        $shop_id = $product->shop_id;
        $shop_name = Shop::find($shop_id)->name;

        $favorite_flag = false;
        if( session()->has('favorite_products') ) {
            $favorite_products = session('favorite_products');
            if( array_key_exists($id, $favorite_products) ) {
                $favorite_flag = true;
            }
        }

        return view('product', ['product' => $product, 'shop_name' => $shop_name, 'favorite_flag' => $favorite_flag]);

    }

    /**
     * お気に入り機能
     **/ 
        // お気に入り商品画面の表示
        public function favoriteProduct() {
            $favorite_products = session('favorite_products');

            // if( !is_null($favorite_products) ) {
            //     $product_id_array = [];
            //     foreach( $favorite_products as $key => $value ) {
            //         $product_id_array[] = $key;
            //     }
        
            //     $product_price_array = [];
            //     $product_count_array = [];
            //     foreach( $product_id_array as $value ) {
            //         $price = $favorite_products[$value]['price'];
            //         $num = $favorite_products[$value]['num'];
            //         $product_price = $price * $num;
            //         $product_price_array[] = $product_price;
            //         $product_count_array[] = $num;
            //     }
        
        
                return view('favorite_product', ['favorite_products' => $favorite_products]);

            // } else {
            //     return view('favorite_product', ['fovorite_products' => $favorite_products]);
            // }

        }

        // お気に入り商品の追加
        public function addFavoriteProduct(Request $request) {

            // ユーザーからのリクエスト
            $add_product_id = $request->id;

            $product = Product::find($add_product_id);

            $add_product = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'num' => $product->num,
            ];

            if( $add_product['image'] == null ) {
                $add_product['image'] = "no_image_logo.png";
            }

            if( !session()->has('favorite_products') ) {
                // セッションにお気に入り商品が存在しないとき
                $favorite_products = [];
                $favorite_products[$add_product_id] = $add_product;
                session(['favorite_products' => $favorite_products]);
                return back()->with('favorite_success', '商品をお気に入り登録しました。');


            } else {
                // セッションにお気に入り商品が存在するとき
                $favorite_products = session('favorite_products');
                $favorite_product_num = count($favorite_products);

                if( $favorite_product_num == 0 ) {
                    // セッションにお気に入りが存在しているが商品が１つも登録されてないとき
                    $favorite_products[$add_product_id] = $add_product;
                } else {
                    // セッションにお気に入りが存在しており、商品がお気に入り登録されているとき
                        // ユーザーからのリクエストが重複しないとき(カート内に新規の商品を入れるとき)
                        foreach( $favorite_products as $key => $value ) {
                            $product_keys[] = $key;
                            $product_values[] = $value;
        
                        }
                        array_push($product_keys, $add_product_id);
                        array_push($product_values, $add_product);
        
                        $favorite_products = array_combine($product_keys, $product_values);

                }

                session(['favorite_products' => $favorite_products]);

                return back()->with('favorite_success', '商品をお気に入り登録しました。');
            }

        }

        // お気に入り商品の削除
        public function deleteFavoriteProduct(Request $request) {
            $delete_product_id = $request->session_favorite_product_id;
    
            $favorite_products = session('favorite_products');
            $favorite_product_num = count($favorite_products);
            
    
            if( array_key_exists($delete_product_id, $favorite_products) ) {
                unset($favorite_products[$delete_product_id]);
                session(['favorite_products' => $favorite_products]);
    
                $favorite_product_num = count($favorite_products);
                if( $favorite_product_num == 0 ) {
                    unset($favorite_products);
                    session()->forget('favorite_products');
                }
    
                return back()->with('delete_favorite_product_success', '商品のお気に入りを解除しました。');
            } else {
                return back()->with('delete_favorite_product_failed', 'お気に入り商品がありません');
            }
        }
    
        // お気に入り商品をすべて削除
        public function deleteAllFavoriteProduct() {
            session()->forget('favorite_products');
            return back()->with('delete_favorite_product_success', 'すべての商品のお気に入りを解除しました。');
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
    // public function purchase(Request $request) {
    //     $id = $request->id;
    //     $number_sold = $request->number_sold;
    //     $product = Product::find($id);
    //     $stock = $product->stock;

    //     if ($stock < $number_sold) {
    //         return back()->with('stock_error', '在庫がありません。');
    //     } else {
    //         $update_stock = $stock - $number_sold;
    //     // dd($update_stock);    



    //         // Product::update('products')
    //         // ->set('stock', '=', $update_stock)
    //         // ->where('id', '=', $id)
    //         // ->save();

    //         \DB::beginTransaction();
    //         try {
    //             \DB::table('products')
    //             ->where('id', $id)
    //             ->update([
    //                 'stock' => $update_stock,
    //             ]);

    //             \DB::commit();
    //             return view('purchased');

    //         } catch(\Throwable $e) {
    //             \DB::rollback();
    //             abort(500);
    //         }


    //     }

    // }

    public function purchase(Request $request) {
        // ショッピングカートに商品が存在しない状態で購入ボタンが押されたとき
        if( !session()->has('shopping_cart') && isset($request->id)) {
            $id = $request->id;
            $num= $request->num;
            $product = Product::find($id);
            $stock = $product->stock;

            if ($stock < $num) {
                return back()->with('stock_error', '在庫がありません。');
            } else {
                $update_stock = $stock - $num;
    
                \DB::beginTransaction();
                try {
                    \DB::table('products')
                    ->where('id', $id)
                    ->update([
                        'stock' => $update_stock,
                    ]);

                    if( !session()->has('purchased_products') ) {
                        $purchased_products[] = $id;
                    } else {
                        $purchased_products = session('purchased_products');
                        $purchased_products[] = $id;
                    }
                    session(['purchased_products' => $purchased_products]);
    
                    \DB::commit();
                    return view('purchased');
    
                } catch(\Throwable $e) {
                    \DB::rollback();
                    abort(500);
                }
            }

        // すでにショッピングカートが存在している状態で商品の単発購入ボタンが押されたとき
        } elseif( session()->has('shopping_cart') && isset($request->id) ) {
            $shopping_cart = session('shopping_cart');

            $add_product_id = $request->id;
            $product = Product::find($add_product_id);
            $add_product = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'num' => $request->num,
            ];
    
            if( $add_product['image'] == null ) {
                $add_product['image'] = "no_image_logo.png";
            }
    


            // セッションにショッピングカートが存在しており、カート内に商品が入っているとき
            if( array_key_exists($add_product_id, $shopping_cart) ) {
                // ユーザーからのリクエストが重複するとき(すでにカート内に入っている商品を追加でカートに入れるとき)
                $shopping_cart[$add_product_id]['num'] += $request->num;
            } else {
                // ユーザーからのリクエストが重複しないとき(カート内に新規の商品を入れるとき)
                foreach( $shopping_cart as $key => $value ) {
                    $product_keys[] = $key;
                    $product_values[] = $value;

                }
                array_push($product_keys, $add_product_id);
                array_push($product_values, $add_product);

                $shopping_cart = array_combine($product_keys, $product_values);

            }

            session(['shopping_cart' => $shopping_cart]);
            return redirect( route('shopping_cart', [ Auth::user()->id, Auth::user()->name ]) );


        } elseif( session()->has('shopping_cart') ) {
        // 商品がショッピングカートに存在している状態で購入ボタンが押されたとき
        
            $shopping_cart = session('shopping_cart');

            $shopping_cart_only_flag = false;
            if( count($shopping_cart) == 1 ) {
                $shopping_cart_only_flag = true;
            }

            $id_array = [];
            foreach( $shopping_cart as $key => $value ) {
                $id_array[] = $key;
            }

            $num_array = [];
            foreach($id_array as $key => $value) {
                $num_value = 'num'.$value;
                $num_array[] = $request->$num_value;
            }
    
            $id_num_array = array_combine($id_array, $num_array);
            foreach( $id_num_array as $key => $value ) {
                $shopping_cart[$key]['num'] = intval($value);
            }



            // 
            $purchased_products = [];

            session(['purchased_products' => $purchased_products]);


            if( !session()->has('purchased_products') ) {
                $purchased_products = [];
            } else {
                $purchased_products = session('purchased_products');
            }
            $purchase_error = [];
            foreach( $id_num_array as $key => $value ) {
                $product = Product::find($key);
                $stock = $product->stock;
                $value = intval($value);
                $update_stock = $stock - $value;


                if( $stock >= $value ) {
                    Product::where('id', '=', $key)
                        ->update([
                            'stock' => $update_stock,
                        ]);
                    
                    $purchased_products[] = $key;
                    unset($shopping_cart[$key]);

                } else {
                    $purchase_error[] = $product->name;
                }
            }
            
            session(['purchased_products' => $purchased_products]);
            if( count($shopping_cart) == 0 ) {
                unset($shopping_cart);
                session()->forget('shopping_cart');
            } else {
                session(['shopping_cart' => $shopping_cart]);
            }

            if( count($purchase_error) == 1 && $shopping_cart_only_flag == true ) {
                return back()->with('purchase_error_only', $purchase_error[0] . 'の購入に失敗しました。');
            }

            if( count($purchase_error) >= 1 && $shopping_cart_only_flag == false ) {
                return view('purchased')->with('purchase_error', $purchase_error);
            }

            return view('purchased');
        
        } else {
            return redirect( route('home') );
        }
    }


    /**
     * ショッピングカート機能
     */

        // ショッピングカートの表示
        public function shoppingCart(Request $request) {
            $shopping_cart = session('shopping_cart');

            if( !is_null($shopping_cart) ) {
                $product_id_array = [];
                foreach( $shopping_cart as $key => $value ) {
                    $product_id_array[] = $key;
                }
        
                $product_price_array = [];
                $product_count_array = [];
                foreach( $product_id_array as $value ) {
                    $price = $shopping_cart[$value]['price'];
                    $num = $shopping_cart[$value]['num'];
                    $product_price = $price * $num;
                    $product_price_array[] = $product_price;
                    $product_count_array[] = $num;
                }
                $total_price = array_sum($product_price_array);
        
                $total_count = array_sum($product_count_array);
        
                $shopping_cart_info = [];
                $shopping_cart_info['total_price'] = $total_price;
                $shopping_cart_info['total_count'] = $total_count;
        
                return view('shopping_cart', ['shopping_cart' => $shopping_cart, 'shopping_cart_info' => $shopping_cart_info]);

            } else {
                return view('shopping_cart', ['shopping_cart' => $shopping_cart]);
            }

        }


        // ショッピングカートに商品を追加
        public function addShoppingCart(Request $product) {
            // ユーザーからのリクエスト
            $add_product_id = $product->id;
            $add_product = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'num' => $product->num,
            ];

            if( $add_product['image'] == null ) {
                $add_product['image'] = "no_image_logo.png";
            }

            if( !session()->has('shopping_cart') ) {
                // セッションにショッピングカートが存在しないとき(カート内に初めて商品を入れるとき)
                $shopping_cart = [];
                $shopping_cart[$add_product_id] = $add_product;
                session(['shopping_cart' => $shopping_cart]);
                return back()->with('cart_success', '商品をカートに追加しました。');


            } else {
                // セッションにショッピングカートが存在するとき(すでにカート内に商品が入っている)
                $shopping_cart = session('shopping_cart');
                $product_incart_num = count($shopping_cart);

                if( $product_incart_num == 0 ) {
                    // セッションにショッピングカートが存在しているが商品が１つも入っていないとき
                    $shopping_cart[$add_product_id] = $add_product;
                } else {
                    // セッションにショッピングカートが存在しており、カート内に商品が入っているとき
                    if( array_key_exists($add_product_id, $shopping_cart) ) {
                        // ユーザーからのリクエストが重複するとき(すでにカート内に入っている商品を追加でカートに入れるとき)
                        $shopping_cart[$add_product_id]['num'] += $product->num;
                    } else {
                        // ユーザーからのリクエストが重複しないとき(カート内に新規の商品を入れるとき)
                        foreach( $shopping_cart as $key => $value ) {
                            $product_keys[] = $key;
                            $product_values[] = $value;
        
                        }
                        array_push($product_keys, $add_product_id);
                        array_push($product_values, $add_product);
        
                        $shopping_cart = array_combine($product_keys, $product_values);
        
                    }

                }

                session(['shopping_cart' => $shopping_cart]);

                return back()->with('cart_success', '商品をカートに追加しました。');
            }
        
        }

        // ショッピングカートの商品を削除
        public function deleteShoppingCart(Request $request) {
            $delete_product_id = $request->session_product_id;

            $shopping_cart = session('shopping_cart');
            $product_incart_num = count($shopping_cart);
            

            if( array_key_exists($delete_product_id, $shopping_cart) ) {
                unset($shopping_cart[$delete_product_id]);
                session(['shopping_cart' => $shopping_cart]);

                $product_incart_num = count($shopping_cart);
                if( $product_incart_num == 0 ) {
                    unset($shopping_cart);
                    session()->forget('shopping_cart');
                }

                return back()->with('delete_shopping_cart_success', 'カートから商品を削除しました');
            } else {
                return back()->with('delete_shopping_cart_failed', 'カートに商品がありません');
            }
        }

        // ショッピングカートの商品をすべて削除
        public function deleteAllShoppingCart() {
            session()->forget('shopping_cart');
            return back()->with('delete_shopping_cart_success', 'カートから商品を削除しました');
        }

    /**
     * 購入履歴
     */
    public function purchasedProduct() {
        $purchased_product_ids = session('purchased_products');

        if( is_null($purchased_product_ids) ) {
            $purchased_products = null;
        } else {
            $purchased_product_info = [];
            foreach( $purchased_product_ids as $value ) {
                $product = Product::find($value);

                $product_info = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                ];

                if( $product_info['image'] == null ) {
                    $product_info['image'] = "no_image_logo.png";
                }

                $purchased_product_info[] = $product_info;
            }

            $purchased_products = array_combine($purchased_product_ids, $purchased_product_info);
        }

        return view('purchased_product', ['purchased_products' => $purchased_products]);
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
            
            if( $product->image ) {
                \Storage::disk('public')->delete('product_images/' . $product->image);
            }

            return back()->with('product_delete_success', '商品を削除しました。');

        } else {
            return back()->with('product_delete_err', 'エラーが発生しました。');
        }


    }


}