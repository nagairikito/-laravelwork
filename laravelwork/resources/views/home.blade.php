<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <li><a href="/shop_orner/{{ Auth::user()->id }}">ショップオーナー</a></li>
        @endif


    </ul>

    <ul>
        <li>アカウントの切り替え機能（ログイン時ほかのアカウントでログインしているばあいの処理）</li>
        <li>画像の貼り付け</li>
        <li>ショップの編集機能</li>
        <li>商品の編集機能</li>
        <li>商品購入処理</li>
    </ul>

    <h2>ショップ一覧</h2>
        <div style="width: 100%;">
            <a href="/shop/0/株式会社 山田">株式会社 山田(エラーメッセージ確認用)</a><br>
            @foreach ( $shops as $shop ) <!-- ShopControllerからわたってきたデータを表示 -->
                <a href="/shop/{{ $shop->id }}/{{ $shop->name }}">{{ $shop->name }}</a><br>
            @endforeach

            {{ $shops->links() }}
        </div>

        <h2>商品一覧</h2>
        <a href="/product/0/avoihaoivh">avoihaoivh(エラーメッセージ確認用)</a><br>
        @foreach ( $products as $product ) <!-- ShopControllerからわたってきたデータを表示 -->
            <a href="/product/{{ $product->id }}/{{ $product->name }}">{{ $product->name }}</a><br>
        @endforeach

        {{ $products->links() }}

</body>
</html>