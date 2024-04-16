<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css"> リセットcss-->
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>ホームページ</title>
</head>
<body>

    <header>
        <form class="search_var">
            <input type="search" placeholder="商品名・ショップ名" style="width: 80%; height: 100%;">
            <input type="submit" value="検索" style="width: 15%; height: 100%;">
        </form>

        @if ( is_null(Auth::user()) )
                <p class="header_nav"><a href="{{ route('login_form') }}">ログイン</a></p>
        @endif

        @if ( Auth::user() )
            <ul class="header_nav_auth">
                <li><a href="{{ route('login_form') }}" >アカウント切り替え</a></li>
                <li><a href="{{ route('shop_orner', [ Auth::user()->id ]) }}">ショップオーナー</a></li>
                <li><a href=""><span style="font-size: 1.4em;">☆</span>お気に入り</a></li>
                <li><a href="{{ route('shopping_cart', [ Auth::user()->id, Auth::user()->name ]) }}">🛒買い物カゴ</a></li>
                <li><a href="">🌐購入履歴</a></li>
            </ul>
        @endif


    </header>

    <nav class="left_nav">
        <table border="0" style="position: absolute; left: 50%; transform: translate(-50%, 0); margin-top: 10px;">
            @foreach( $categorys as $category )
            <tr>
                <td><a href="">{{ $category }}</a></td>
            </tr>
            @endforeach
        </table>
    </nav>

    <nav class="right_nav">
        <ul class="account_info_unit">
            <li style="font-weight: bold; text-align: center; display: block;">ユーザー情報</li>
            <li>ユーザー名:</li>
            @if( Auth::user() )
                <li>{{ Auth::user()->name }}</li>
                <li>メールアドレス:</li>
                <li style="overflow-wrap: break-word;">{{ Auth::user()->email }}</li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                    @csrf
                        <input type="submit" value="ログアウト">
                    </form>
            @elseif( is_null(Auth::user()) )
                <li style="margin: 5px;">ゲスト</li>
                <button style="margin: 0 auto; display: block;"><a href="{{ route('login_form') }}">ログイン</a></button>
            @endif
            
        </ul>

        <ul class="nav_info">
            <li><a href="{{ route('register_form') }}">新規作成</a></li>
            @if ( is_null(Auth::user()) )
            <li><a href="{{ route('login_form') }}">ログイン</a></li>
            @endif
            @if ( Auth::user() )
            <li><a href="{{ route('login_form') }}" >👤アカウント切り替え</a></li>
            <li><a href="{{ route('shop_orner', [ Auth::user()->id ]) }}">⌂ショップオーナー</a></li>
            <li><a href=""><span style="font-size: 1.4em;">☆</span>お気に入り</a></li>
            <li><a href="{{ route('shopping_cart', [ Auth::user()->id, Auth::user()->name ]) }}">🛒買い物カゴ</a></li>
            <li><a href="">🌐購入履歴</a></li>
            @endif
        </ul>

    </nav>

    <main>

        <ul>
            <li>登録した商品をcsv出力ができる。</li>
            <li>カート機能</li>
            <li>カテゴリー付与</li>
            <li></li>
        </ul>


        @if ( session('login_success') ) <!-- ログイン成功時のメッセージ -->
        <p class="success">{{ session('login_success') }}</p>
        @endif

        @if ( session('logout_msg') ) <!-- ログアウトメッセージ -->
            <p class="success">{{ session('logout_msg') }}</p>
        @endif

        @if ( session('shopDetail_err_msg') ) <!-- ショップのリンクを踏んだ時にデータがなかった場合(ShopController.shopDetaiの$shopにデータがない場合もしくは不一致の場合)エラーメッセージを表示する -->
            <p class="fail">{{ session('shopDetail_err_msg') }}</p>
        @endif

        @if ( session('productDetail_err_msg') ) <!-- 商品詳細のエラーメッセージ -->
            <p class="fail">{{ session('productDetail_err_msg') }}</p>
        @endif

        @if( session('user_shop_error') )
            <p class="fail">{{ session('user_shop_error') }}</p>
        @endif


        <h2>ショップ一覧</h2>
        <div>
            <ul class="unit_frame">
                <!-- <li class="unit">
                    <a href="{{ route('shop_detail', [0, '株式会社 山田']) }}">
                        <div>
                            <img class="unit_image" src="https://th.bing.com/th/id/OIP.e2D7uiFBePfio6qxhEGQlwHaHa?w=197&h=197&c=7&r=0&o=5&cb=11&pid=1.7">
                            <p>株式会社 山田(エラーメッセージ確認用)</p>
                        </div>
                    </a>
                </li> -->
                @foreach ( $shops as $shop ) <!-- ShopControllerからわたってきたデータを表示 -->
                <li class="unit">
                    <a href="{{ route('shop_detail', [$shop->id, $shop->name]) }}">
                        <div>

                        @if( is_null($shop->image) )
                        <img class="unit_image" src="{{ asset('storage/shop_images/no_image_logo.png') }}">
                        @else
                            <img class="unit_image" src="{{ asset('storage/shop_images' . $shop->image) }}">
                        @endif

                            <p>{{ $shop->name }}</p>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
            <div style="position: relative; width: 100%; height: 50px;"><div style="position: absolute; left: 41%; tranform: translate(-50%, 0);">{{ $shops->links() }}</div></div>
        </div>


        <h2>商品一覧</h2>
            <ul class="unit_frame">
                <!-- <li class="unit">
                    <a href="{{ route('product_detail', [0, 'avoihaoivh']) }}">
                        <div>
                            <p>avoihaoivh(エラーメッセージ確認用)</p>
                            <img class="unit_image" src="https://th.bing.com/th/id/OIP.rPn9QhUClxoV95i1_D5DNwHaE7?w=262&h=180&c=7&r=0&o=5&cb=11&pid=1.7">
                            <p>￥1980円</p>
                        </div>
                    </a>
                </li> -->
                @foreach ( $products as $product ) <!-- ShopControllerからわたってきたデータを表示 -->
                <li class="unit">
                    <a href="{{ route('product_detail', [$product->id, $product->name]) }}">
                        <div>
                            <p>{{ $product->name }}</p>

                            @if( is_null($product->image) )
                                <img class="unit_image" src="{{ asset('storage/product_images/no_image_logo.png') }}">
                            @else
                                <img class="unit_image" src="{{ asset('storage/product_images' . $product->image) }}">
                            @endif

                            <p>￥{{ $product->price }}円</p>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
            <div style="position: relative; width: 100%; height: 50px;"><div style="position: absolute; left: 41%; tranform: translate(-50%, 0);">{{ $products->links() }}</div></div>


        <h2>人気の商品</h2>
        <ul class="unit_frame">
            @foreach ( $popular_products as $popular_product ) <!-- ShopControllerからわたってきたデータを表示 -->
                <li class="unit">
                    <a href="{{ route('product_detail', [$popular_product->id, $popular_product->name]) }}">
                        <div>
                            <p>{{ $popular_product->name }}</p>

                            @if( is_null($popular_product->image) )
                                <img class="unit_image" src="{{ asset('storage/product_images/no_image_logo.png') }}">
                            @else
                                <img class="unit_image" src="{{ asset('storage/product_images' . $popular_product->image) }}">
                            @endif

                            <p>￥{{ $popular_product->price }}円</p>
                        </div>
                    </a>
                </li>
            @endforeach    
        </ul>
        <div style="position: relative; width: 100%; height: 50px;"><div style="position: absolute; left: 41%; tranform: translate(-50%, 0);">{{ $popular_products->links() }}</div></div>
    
    
    </main>

</body>
</html>