<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css"> -->

    <title>ホームページ</title>
</head>
<body>
    @if ( session('login_success') ) <!-- ログイン成功時のメッセージ -->
        <p>{{ session('login_success') }}</p>
    @endif

    @if ( session('logout_msg') ) <!-- ログアウトメッセージ -->
        {{ session('logout_msg') }}
    @endif

    @if ( session('shopDetail_err_msg') ) <!-- ショップのリンクを踏んだ時にデータがなかった場合(ShopController.shopDetaiの$shopにデータがない場合もしくは不一致の場合)エラーメッセージを表示する -->
        <p style="color: red;">{{ session('shopDetail_err_msg') }}</p>
    @endif

    @if ( session('productDetail_err_msg') ) <!-- 商品詳細のエラーメッセージ -->
        <p style="color: red;">{{ session('productDetail_err_msg') }}</p>
    @endif

    @if( session('user_shop_error') )
        <p style="color: red;">{{ session('user_shop_error') }}</p>
    @endif


    @if ( Auth::user() )
        <ul style="list-style: none;">
            <li style="font-weight: bold;">ユーザー情報</li>
            <li>ユーザー名: {{ Auth::user()->name }}</li>
            <li>メールアドレス: {{ Auth::user()->email }}</li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                @csrf
                    <input type="submit" value="ログアウト">
                </form>
            </li>
        </ul>
    @endif

    <ul style="list-style: none;">
        <li><a href="{{ route('register_form') }}">新規作成</a></li>
        @if ( is_null(Auth::user()) )
        <li><a href="{{ route('login_form') }}">ログイン</a></li>
        @endif
        @if ( Auth::user() )
        <li><a href="{{ route('login_form') }}">別のアカウントでログイン</a></li>
        <li><a href="/shop_orner/{{ Auth::user()->id }}">ショップオーナー</a></li>
        @endif


    </ul>

    <ul>
        <li>アカウントの切り替え機能（ログイン時ほかのアカウントでログインしているばあいの処理）</li>
        <li>画像の貼り付け</li>
    </ul>

    <form>
        <input type="search" placeholder="商品名・ショップ名">
        <input type="submit" value="検索">
    </form>

    <h2>ショップ一覧</h2>
        <ul style="list-style: none; display: flex; justify-content: flex-start; padding: 0px;">
            <li style="0 5px 0 0; background-color: lightblue;">
                <a href="{{ route('shop_detail', [0, '株式会社 山田']) }}">
                    <div style="padding: 10px;">
                        <img src="https://th.bing.com/th/id/OIP.e2D7uiFBePfio6qxhEGQlwHaHa?w=197&h=197&c=7&r=0&o=5&cb=11&pid=1.7">
                        <p>株式会社 山田(エラーメッセージ確認用)</p>
                    </div>
                </a>
            </li>
            @foreach ( $shops as $shop ) <!-- ShopControllerからわたってきたデータを表示 -->
            <li style="margin: 5px; background-color: lightblue;">
                <a href="{{ route('shop_detail', [$shop->id, $shop->name]) }}" style="padding: 10px;">
                    <div style="padding: 10px;">

                    @if( is_null($shop->image) )
                    <img src="{{ asset('storage/shop_images/no_image_logo.png') }}">
                    @else
                        <img src="{{ asset('storage/shop_images' . $shop->image) }}">
                    @endif

                        <p>{{ $shop->name }}</p>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
        {{ $shops->links() }}



    <h2>商品一覧</h2>
        <ul style="list-style: none; display: flex; justify-content: flex-start; padding: 0px;">
            <li style="0 5px 0 0; background-color: lightblue;">
                <a href="{{ route('product_detail', [0, 'avoihaoivh']) }}">
                    <div style="padding: 10px;">
                        <p>avoihaoivh(エラーメッセージ確認用)</p>
                        <img src="https://th.bing.com/th/id/OIP.rPn9QhUClxoV95i1_D5DNwHaE7?w=262&h=180&c=7&r=0&o=5&cb=11&pid=1.7">
                        <p>￥1980円</p>
                    </div>
                </a>
            </li>
            @foreach ( $products as $product ) <!-- ShopControllerからわたってきたデータを表示 -->
            <li style="margin: 5px; background-color: lightblue;">
                <a href="{{ route('product_detail', [$product->id, $product->name]) }}">
                    <div style="padding: 10px;">
                        <p>{{ $product->name }}</p>

                    @if( is_null($product->image) )
                        <img src="{{ asset('storage/product_images/no_image_logo.png') }}">
                    @else
                        <img src="{{ asset('storage/product_images' . $product->image) }}">
                    @endif

                        <p>￥{{ $product->price }}円</p>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
        {{ $products->links() }}

</body>
</html>