<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use withFileUploads;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Category;
use App\Models\FavoriteProduct;
use App\Models\ShoppingCart;
use App\Models\PurchasedProduct;
// use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\ProductRegisterRequest;

class ProductController extends Controller
{
    /**
     * 検索機能
     */
    public function search(Request $request) {
        $keyword = $request->input('keyword');
        if( !empty($keyword) ) {
            $pre_result = Product::query()->select([
                'p.id',
                'p.shop_id',
                'p.name',
                'p.price',
                'p.stock',
                'p.discription',
                'p.image',
                'p.access_count',
                's.name as shop_name',
            ])
            ->from('products as p')
            ->join('shops as s', function($join) {
                $join->on('p.shop_id', '=', 's.id');
            })
            ->where('p.name', 'LIKE', "%{$keyword}%")
            ->orWhere('s.name', 'LIKE', "%{$keyword}%")
            ->get();
            // ->paginate(10);

            // $result = $result->paginate(10);
            foreach( $pre_result as $key => $product_info) {
                if( $product_info['image'] == null ) {
                    $product_info['image'] = 'no_image_logo.png';
                }
    
            }

            $pre_result_array = [];
            foreach( $pre_result as $key => $value ) {
                $pre_result_array[] = $value;
            }

            // 検索にマッチした総件数
            $result_count = count($pre_result);

            // アクセスしているページ数
            $page = $request->page;

            // 1ページあたりの表示件数
            $perpage = 10;

            // アクセスしているページの表示内容(検索結果10件分)
            $per_page_result = array_slice($pre_result_array, ($page * $perpage)-$perpage, ($page * $perpage)-1 );

            // ページごとに表示する結果を分ける(2ページ目は21～30件数など)
            $result = new LengthAwarePaginator($per_page_result, $result_count, $perpage, $page, array('path' => '/search'));

            return view('search_result', ['keyword' => $keyword, 'result' => $result, 'result_count' => $result_count, 'pagenate_params' => [ 'keyword' => $keyword ] ]);

        }

        return redirect( route('home') );
    }


    /**
     * カテゴリー別商品の表示
     */
    public function category($request) {
        $result = Product::select([
            'p.id',
            'p.shop_id',
            'p.name',
            'p.price',
            'p.stock',
            'p.discription',
            'p.image',
            'p.access_count',
            's.name as shop_name',
            'p.category_id',
            'c.category',
        ])
        ->from('products as p')
        ->join('shops as s', function($join) {
            $join->on('p.shop_id', '=', 's.id');
        })
        ->join('categories as c', function($join) {
            $join->on('p.category_id', '=', 'c.id');
        })
        ->where('p.category_id', '=', $request)
        ->paginate(10);


        foreach( $result as $key => $product_info) {
            if( $product_info['image'] == null ) {
                $product_info['image'] = 'no_image_logo.png';
            }
        }

        $title = $result['p.category'];
        $result_count = count($result);

        return view('category_product', ['result' => $result, 'title' => $title, 'result_count' => $result_count]);
        
    }


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
        $shop = Shop::find($shop_id);

        $favorite_flag = false;
        if( session()->has('favorite_products') ) {
            $favorite_products = session('favorite_products');
            if( array_key_exists($id, $favorite_products) ) {
                $favorite_flag = true;
            }
        }

        return view('product', ['product' => $product, 'shop' => $shop, 'favorite_flag' => $favorite_flag]);

    }

    /**
     * お気に入り機能
     **/ 
        // お気に入り商品画面の表示
        public function favoriteProduct() {
            $user_id = Auth::user()->id;

            // DBからお気に入り商品情報を取得
            $favorite_products_info = Product::select([
                'p.id',
                'p.name',
                'p.price',
                'p.image',
            ])
            ->from('products as p')
            ->join('favoriteproducts as fp', function($join) {
                $join->on('p.id', '=', 'fp.product_id');
            })
            ->where('fp.user_id', '=', $user_id)
            ->get();


            $product_keys = [];
            $product_info = [];

            foreach( $favorite_products_info as $favorite_product ) {
                $product_keys[] = $favorite_product->id;
                $product_info[] = $favorite_product;

            }

            $favorite_products = array_combine($product_keys, $product_info);

            foreach( $favorite_products as $key => $value ) {
                if( $value["image"] == null ) {
                        $favorite_products[$key]["image"] = "no_image_logo.png";
                }
            }

            session(['favorite_products' => $favorite_products]);


            $favorite_products = session('favorite_products');
       
            return view('favorite_product', ['favorite_products' => $favorite_products]);

        }

        // お気に入り商品の追加
        public function addFavoriteProduct(Request $request) {
            // ユーザーからのリクエスト
            $add_product_id = $request->id;            
            $user_id = Auth::user()->id;

            // お気に入りに追加する商品情報
            $product = Product::find($add_product_id);

            $add_product = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
            ];

            if( $add_product['image'] == null ) {
                $add_product['image'] = "no_image_logo.png";
            }            

            // DBからお気に入り商品情報を取得
            $favorite_products_info = Product::select([
                'p.id',
                'p.name',
                'p.price',
                'p.image',
            ])
            ->from('products as p')
            ->join('favoriteproducts as fp', function($join) {
                $join->on('p.id', '=', 'fp.product_id');
            })
            ->where('fp.user_id', '=', $user_id)
            ->get();


                $product_keys = [];
                $product_info = [];

                foreach( $favorite_products_info as $favorite_product ) {
                    $product_keys[] = $favorite_product->id;
                    $product_info[] = $favorite_product;

                }

                $favorite_products = array_combine($product_keys, $product_info);

                foreach( $favorite_products as $key => $value ) {
                    if( $value["image"] == null ) {
                            $favorite_products[$key]["image"] = "no_image_logo.png";
                    }
                }

                session(['favorite_products' => $favorite_products]);

            // }


            // お気に入り追加処理
            if( !session()->has('favorite_products') ) {
                // セッションにお気に入り商品が存在しないとき
                $favorite_products = [];
                $favorite_products[$add_product_id] = $add_product;
                session(['favorite_products' => $favorite_products]);

                // お気に入り商品をDBにも登録する
                FavoriteProduct::query()->create([
                    'user_id' => $user_id,
                    'product_id' => $add_product_id,
                ]);

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
                            $product_keys2[] = $key;
                            $product_values2[] = $value;
        
                        }
                        array_push($product_keys2, $add_product_id);
                        array_push($product_values2, $add_product);

                        $favorite_products = array_combine($product_keys2, $product_values2);

                }

                session(['favorite_products' => $favorite_products]);

                // お気に入り商品をDBにも登録する
                FavoriteProduct::query()->create([
                    'user_id' => $user_id,
                    'product_id' => $add_product_id,
                ]);
                

                return back()->with('favorite_success', '商品をお気に入り登録しました。');
            }

        }

        // お気に入り商品の削除
        public function deleteFavoriteProduct(Request $request) {
            $delete_product_id = $request->session_favorite_product_id;
    
            $favorite_products = session('favorite_products');
            
    
            if( array_key_exists($delete_product_id, $favorite_products) ) {
                unset($favorite_products[$delete_product_id]);

                session(['favorite_products' => $favorite_products]);
    
                FavoriteProduct::where('user_id', Auth::user()->id)->where('product_id', $delete_product_id)->delete();

    
                return back()->with('delete_favorite_product_success', '商品のお気に入りを解除しました。');
            } else {
                return back()->with('delete_favorite_product_failed', 'お気に入り商品がありません');
            }
        }
    
        // お気に入り商品をすべて削除
        public function deleteAllFavoriteProduct() {
            session()->forget('favorite_products');

            FavoriteProduct::where('user_id', Auth::user()->id)->delete();

            return back()->with('delete_favorite_product_success', 'すべての商品のお気に入りを解除しました。');
        }
                

    

    /**
     * 商品購入画面を表示
     * 
     */
    public function purchaseForm($id) {
        $product = Product::find($id);

        $shop_id = $product->shop_id;
        $shop = Shop::find($shop_id);
        $shop_user_id = $shop->user_id;

        return view('purchase_form', ['product' => $product, 'shop_user_id' => $shop_user_id]);
    }


    /**
     * 商品購入処理
     */
    public function purchase(Request $request) {
        $user_id = Auth::user()->id;

        // DBからショッピングカート情報を取得
        $shopping_cart_info = ShoppingCart::select([
            'p.id',
            'p.name',
            'p.price',
            'p.image',
            'sc.num',
        ])
        ->from('products as p')
        ->join('shoppingcart as sc', function($join) {
            $join->on('p.id', '=', 'sc.product_id');
        })
        ->where('sc.user_id', '=', $user_id)
        ->get();

        if( empty($shopping_cart_info) ) {
            $shopping_cart = [];
        } else {

            $product_keys = [];
            $product_info = [];

            foreach( $shopping_cart_info as $shopping_cart_product ) {
                $product_keys[] = $shopping_cart_product->id;
                $product_info[] = $shopping_cart_product;

            }

            $shopping_cart = array_combine($product_keys, $product_info);

            foreach( $shopping_cart as $key => $value ) {
                if( $value["image"] == null ) {
                    $shopping_cart[$key]["image"] = "no_image_logo.png";
                }
            }
        }
        session(['shopping_cart' => $shopping_cart]);


        // DBから購入履歴情報を取得
        $db_purchased_products_info = PurchasedProduct::select([
            'p.id',
            'p.name',
            'p.price',
            'p.image',
        ])
        ->from('products as p')
        ->join('purchasedproducts as pp', function($join) {
            $join->on('p.id', '=', 'pp.product_id');
        })
        ->where('pp.user_id', '=', $user_id)
        ->get();

        if( empty($db_purchased_product_info) ) {
            $purchased_products = [];
        } else {
            $product_keys = [];
            $product_info = [];

            foreach( $db_purchased_products_info as $db_purchased_product ) {
                $product_keys[] = $db_purchased_product->id;
                $product_info[] = $db_purchased_product;

            }

            $purchased_products = array_combine($product_keys, $product_info);

            foreach( $purchased_products as $key => $value ) {
                if( $value["image"] == null ) {
                    $purchased_products[$key]["image"] = "no_image_logo.png";
                }
            }
        }
        session(['purchased_products' => $purchased_products]);


        // ショッピングカートに商品が存在しない状態で購入ボタンが押されたとき
        if( empty($shopping_cart) && isset($request->id)) {

            $id = $request->id;
            $num= $request->num;
            $product = Product::find($id);
            $stock = $product->stock;

            if ($stock < $num) {
                // 在庫数が購入数を上回ったとき
                return back()->with('stock_error', '在庫がありません。');
            } else {
                // 在庫数が問題ない場合
                $update_stock = $stock - $num;
    
                // DBに上書き
                Product::where('id', '=', $id)
                ->update([
                    'stock' => $update_stock,
                ]);

                // 購入した商品の情報
                $purchased_product = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                ];
                if( $purchased_product['image'] == null ) {
                    $purchased_product['image'] = "no_image_logo.png";
                }


                if( empty( $purchased_products ) ) {
                    $purchased_products = [];
                    $purchased_products[$id] = $purchased_product;
                } else {
                    $product_keys = [];
                    $product_values = [];
        
                    foreach( $db_purchased_products_info as $db_purchased_product ) {
                        $product_keys[] = $db_purchased_product->id;
                        $product_values[] = $db_purchased_product;
        
                    }
                    array_push($product_keys, $purchased_product['id']);
                    array_push($product_values, $purchased_product);
    
        
                    $purchased_products = array_combine($product_keys, $product_info);
        
                    foreach( $purchased_products as $key => $value ) {
                        if( $value["image"] == null ) {
                            $purchased_products[$key]["image"] = "no_image_logo.png";
                        }
                    }

                }

                // DBに購入履歴を記録する
                PurchasedProduct::create([
                    'user_id' => $user_id,
                    'product_id' => $id,
                ]);

                session(['purchased_products' => $purchased_products]);
    
                return view('purchased');
    
            }

        // すでにショッピングカートが存在している状態で商品の単発購入ボタンが押されたとき
        } elseif( !empty($shopping_cart) && isset($request->id) ) {

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
    


            // ショッピングカートに商品が存在している場合
            if( array_key_exists($add_product_id, $shopping_cart) ) {
                // ユーザーからのリクエストが重複するとき(すでにカート内に入っている商品を追加でカートに入れるとき)
                $update_num = $shopping_cart[$add_product_id]['num'] + $request->num;
                $shopping_cart[$add_product_id]['num'] = $update_num;

                // DBにも上書きする
                ShoppingCart::where('user_id', '=', $user_id)
                ->where('product_id', '=', $add_product_id)
                ->update([
                    'num' => $update_num,
                ]);

            } else {
                // ユーザーからのリクエストが重複しないとき(カート内に新規の商品を入れるとき)
                $product_keys = [];
                $product_values = [];
                foreach( $shopping_cart as $key => $value ) {
                    $product_keys[] = $key;
                    $product_values[] = $value;
                }

                array_push($product_keys, $add_product_id);
                array_push($product_values, $add_product);

                $shopping_cart = array_combine($product_keys, $product_values);

                // DBにも記録する
                ShoppingCart::create([
                    'user_id' => $user_id,
                    'product_id' => $add_product_id,
                    'num' => $add_product['num'],
                ]);

            }

            session(['shopping_cart' => $shopping_cart]);
            return redirect( route('shopping_cart', [ Auth::user()->id, Auth::user()->name ]) );


        } elseif( isset($shopping_cart) && is_null($request->id) ) {
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
                $num_value = 'num'.$value; // ここの処理はshopping_cart.blade.phpのformのname="{{ 'num' . $value['id'] }}"を$num_valueという変数に格納している
                $num_array[] = $request->$num_value; // そしてここの処理でformから送られてきた個数を取得して、$num_arrayという配列に格納している
            }
    
            $id_num_array = array_combine($id_array, $num_array);
            foreach( $id_num_array as $key => $value ) {
                $shopping_cart[$key]['num'] = intval($value);
            }


            // 購入処理と購入履歴の追加
            $purchase_error = [];
            foreach( $id_num_array as $key => $value ) {
                $product = Product::find($key);
                $stock = $product->stock;
                $value = intval($value);
                $update_stock = $stock - $value;


                if( $stock >= $value ) {
                    // ProductDBの更新
                    Product::where('id', '=', $key)
                        ->update([
                            'stock' => $update_stock,
                        ]);
                    
                    // 購入履歴の追加
                    $purchased_products[$key] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image,
                    ];
                    if( $purchased_products[$key]['image'] == null ) {
                        $purchased_products[$key]['image'] = "no_image_logo.png";
                    }

                    PurchasedProduct::create([
                        'user_id' => $user_id,
                        'product_id' => $key,
                    ]);

                    // ショッピングカート内の商品の削除とShoppingCartDBの更新
                    unset($shopping_cart[$key]);
                    
                    ShoppingCart::where('user_id', '=', $user_id)
                    ->where('product_id', '=', $key)
                    ->delete();

                } else {
                    $purchase_error[] = $product->name;
                }
            }
            
            session(['purchased_products' => $purchased_products]);
            session(['shopping_cart' => $shopping_cart]);



            if( count($purchase_error) == 1 && $shopping_cart_only_flag == true ) {
                return back()->with('purchase_error_only', $purchase_error[0] . 'の購入に失敗しました。');
            }

            if( count($purchase_error) >= 1 && $shopping_cart_only_flag == false ) {
                return view('purchased')->with('purchase_error', $purchase_error);
            }

            return view('purchased');
        
        } else {
            return redirect( route('home') )->with('purchased_error', 'エラーが発生しました。');
        }
    }


    /**
     * ショッピングカート機能
     */

        // ショッピングカートの表示
        public function shoppingCart(Request $request) {

            // ログインユーザーid
            $user_id = Auth::user()->id;

            // ショッピングカートを定義
            $shopping_cart = [];
            $total_price = 0;
            $total_num = 0;


            // DBからショッピングカート情報を取得
            $shopping_cart_info = ShoppingCart::select([
                'p.id',
                'p.name',
                'p.price',
                'p.image',
                'sc.num',
            ])
            ->from('products as p')
            ->join('shoppingcart as sc', function($join) {
                $join->on('p.id', '=', 'sc.product_id');
            })
            ->where('sc.user_id', '=', $user_id)
            ->get();
    

            // if( count($shopping_cart_info) > 0 && !session()->has('shopping_cart') ) {
            if( count($shopping_cart_info) > 0 ) {

                $product_keys = [];
                $product_info = [];
    
                foreach( $shopping_cart_info as $shopping_cart_product ) {
                    $product_keys[] = $shopping_cart_product->id;
                    $product_info[] = $shopping_cart_product;
    
                }
    
                $shopping_cart = array_combine($product_keys, $product_info);

                foreach( $shopping_cart as $key => $value ) {
                    if( $value["image"] == null ) {
                        $shopping_cart[$key]["image"] = "no_image_logo.png";
                    }

                    $total_price += $value["price"];
                    $total_num += $value["num"];
                }
        
            }

            session(['shopping_cart' => $shopping_cart]);

            return view('shopping_cart', ['shopping_cart' => $shopping_cart, 'total_price' => $total_price, 'total_num' => $total_num]);

        }


        // ショッピングカートに商品を追加
        public function addShoppingCart(Request $product) {
            // ログインユーザーid
            $user_id = Auth::user()->id;

            // DBからショッピングカート情報を取得
            $shopping_cart = session('shopping_cart');

            $shopping_cart_info = ShoppingCart::select([
                'p.id',
                'p.name',
                'p.price',
                'p.image',
                'sc.num',
            ])
            ->from('products as p')
            ->join('shoppingcart as sc', function($join) {
                $join->on('p.id', '=', 'sc.product_id');
            })
            ->where('sc.user_id', '=', $user_id)
            ->get();
    
    
            if( count($shopping_cart_info) > 0 ) {

                $product_keys = [];
                $product_info = [];
    
                foreach( $shopping_cart_info as $shopping_cart_product ) {
                    $product_keys[] = $shopping_cart_product->id;
                    $product_info[] = $shopping_cart_product;
    
                }
    
                $shopping_cart = array_combine($product_keys, $product_info);
    
                foreach( $shopping_cart as $key => $value ) {
                    if( $value["image"] == null ) {
                        $purchased_products[$key]["image"] = "no_image_logo.png";
                    }
                }
    
                session(['shopping_cart' => $shopping_cart]);
        
            }
            
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


            if( empty($shopping_cart) ) {
                // ショッピングカートに商品が存在しないとき
                $shopping_cart[$add_product_id] = $add_product;
                session(['shopping_cart' => $shopping_cart]);

                // DBにも記録する
                ShoppingCart::create([
                    'user_id' => $user_id,
                    'product_id' => $add_product_id,
                    'num' => $add_product['num'],
                ]);

                return back()->with('cart_success', '商品をカートに追加しました。');


            } else {
                // ショッピングカートに商品が存在していて新たに商品を追加するとき
                if( array_key_exists($add_product_id, $shopping_cart) ) {
                    // ユーザーからのリクエストが重複するとき(すでにカート内に入っている商品を追加でカートに入れるとき)
                    $update_num = $shopping_cart[$add_product_id]['num'] + $product->num;
                    $shopping_cart[$add_product_id]['num'] = $update_num;

                    // DBに上書き
                    ShoppingCart::where('user_id', '=', $user_id)
                    ->where('product_id', '=', $add_product_id)
                    ->update([
                        'num' => $update_num,
                    ]);
                } else {
                    // ユーザーからのリクエストが重複しないとき(カート内に新規の商品を入れるとき)
                    $product_keys = [];
                    $product_values = [];

                    foreach( $shopping_cart as $key => $value ) {
                        $product_keys[] = $key;
                        $product_values[] = $value;
    
                    }
                    array_push($product_keys, $add_product_id);
                    array_push($product_values, $add_product);
    
                    $shopping_cart = array_combine($product_keys, $product_values);

                    // DBにも記録する
                    ShoppingCart::create([
                        'user_id' => $user_id,
                        'product_id' => $add_product_id,
                        'num' => $add_product['num'],
                    ]);

                }

                session(['shopping_cart' => $shopping_cart]);

                return back()->with('cart_success', '商品をカートに追加しました。');
            }
        
        }

        // ショッピングカートの商品を削除
        public function deleteShoppingCart(Request $request) {
            // ログインユーザーid
            $user_id = Auth::user()->id;

            // DBからショッピングカート情報を取得
            $shopping_cart = session('shopping_cart');

            $shopping_cart_info = ShoppingCart::select([
                'p.id',
                'p.name',
                'p.price',
                'p.image',
                'sc.num',
            ])
            ->from('products as p')
            ->join('shoppingcart as sc', function($join) {
                $join->on('p.id', '=', 'sc.product_id');
            })
            ->where('sc.user_id', '=', $user_id)
            ->get();
    
    
            // if( count($shopping_cart_info) > 0 && !session()->has('shopping_cart') ) {
            if( count($shopping_cart_info) > 0 ) {

                $product_keys = [];
                $product_info = [];
    
                foreach( $shopping_cart_info as $shopping_cart_product ) {
                    $product_keys[] = $shopping_cart_product->id;
                    $product_info[] = $shopping_cart_product;
    
                }
    
                $shopping_cart = array_combine($product_keys, $product_info);
    
                foreach( $shopping_cart as $key => $value ) {
                    if( $value["image"] == null ) {
                        $purchased_products[$key]["image"] = "no_image_logo.png";
                    }
                }
    
                session(['shopping_cart' => $shopping_cart]);
        
            }
            

            // ユーザーからのリクエスト
            $delete_product_id = $request->session_product_id;            

            if( array_key_exists($delete_product_id, $shopping_cart) ) {
                unset($shopping_cart[$delete_product_id]);
                session(['shopping_cart' => $shopping_cart]);

                // DBにも記録する
                ShoppingCart::where('user_id', $user_id)
                ->where('product_id', $delete_product_id)
                ->delete();


                return back()->with('delete_shopping_cart_success', 'カートから商品を削除しました');
            } else {
                return back()->with('delete_shopping_cart_failed', 'カートに商品がありません');
            }
        }

        // ショッピングカートの商品をすべて削除
        public function deleteAllShoppingCart() {
            // ログインユーザーid
            $user_id = Auth::user()->id;

            session()->forget('shopping_cart');
            $shopping_cart = null;
            session(['shopping_cart' => $shopping_cart]);

            ShoppingCart::where('user_id', $user_id)->delete();

            return back()->with('delete_shopping_cart_success', 'カートから商品を削除しました');
        }

    /**
     * 購入履歴
     */
    public function purchasedProduct() {
        $user_id = Auth::user()->id;

        // DBから購入履歴情報を取得
        $purchased_products_info = PurchasedProduct::select([
            'p.id',
            'p.name',
            'p.price',
            'p.image',
        ])
        ->from('products as p')
        ->join('purchasedproducts as pp', function($join) {
            $join->on('p.id', '=', 'pp.product_id');
        })
        ->where('pp.user_id', '=', $user_id)
        ->get();


        if( empty($purchased_products_info) ) {
            $purchased_products = [];
        } else {
            $product_keys = [];
            $product_info = [];

            foreach( $purchased_products_info as $purchased_product ) {
                $product_keys[] = $purchased_product->id;
                $product_info[] = $purchased_product;

            }

            $purchased_products = array_combine($product_keys, $product_info);

            foreach( $purchased_products as $key => $value ) {
                if( $value["image"] == null ) {
                    $purchased_products[$key]["image"] = "no_image_logo.png";
                }
            }
        }
        
        session(['purchased_products' => $purchased_products]);
        

        return view('purchased_product', ['purchased_products' => $purchased_products]);
    }

    /**
     * 商品登録フォームを表示する
     * 
     */
    public function productRegisterForm($shop_id) {

        $int_shop_id = intval($shop_id);

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

        return view('product_register_form', ['shop_id' => $int_shop_id, 'categories' => $categories]);
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
                    'category_id' => $request['category_id'],
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
                        'category_id' => $request['category_id'],
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
     * CSV出力
     */
    public function CSVFile(Request $request) {
        $product_exist_flag = $request->product_exist_flag;
        if( $product_exist_flag == false ) {
            return back()->with('csv_fail', '商品情報がありません。');
        }

        $shop_id = intval($request->shop_id);
        $shop_name = $request->shop_name;
        
        // DBからカラム情報を取得
        $column_data = DB::select("show full columns from products");
        $columns = [];
        foreach( $column_data as $key => $value ) {
            $column = $value->Field;
            $columns[] = $column;
        }

        // shopに紐づいている商品をすべて取得
        $products = Product::select([
            'id',
            'shop_id',
            'name',
            'price',
            'stock',
            'discription',
            'image',
            'category_id',
            'access_count',
            'created_at',
            'updated_at',
        ])
        ->from('products')
        ->where('shop_id', '=', $shop_id)
        ->get();


        $csv_data = [];
        $product_info = [];
        foreach( $products as $key => $product ) {
            foreach( $columns as $key => $column_name ) {
                $product_info[] = $product->$column_name;

            }
            $csv_data[] = $product_info;
            $product_info = [];
        }

        // DBのカラム情報とshopに紐づいている商品のデータを結合
        array_unshift($csv_data, $columns);

        // CSVファイルの作成と出力
        $file_name = $shop_name . ".scv";
        $url = "./" . $file_name;
    
        if( file_exists($url) ) {
            unlink($url);
        }

        foreach( $csv_data as $key => $value ) {
            $column_count = count($value);
            foreach( $value as $key => $value ) {
                // $csv = mb_convert_variables('SJIS', 'UTF-8', $value);
                $csv = mb_convert_encoding($value, 'SJIS', 'UTF-8');

                $fp = fopen($url, "a");
                if($fp) {
                    if( is_null($csv) ) {
                        $csv = " "; 
                    }
                    fwrite($fp, $csv . ",");
                    if( $key == $column_count - 1 ) {
                        fwrite($fp, "\n");
                    }
                    fclose($fp);
                }
            }
        }

        return back()->with('csv_success', 'CSV出力しました。');

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
                    // 'image' => $request->image,
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