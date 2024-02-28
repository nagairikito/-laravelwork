<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyShops</title>
</head>
<body>
    <h1>MyShops</h1>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    <ul style="list-style: none;">
        <li><p style="font-weight: bold;">ログインユーザー</p></li>
        <li>ユーザー名 : {{ Auth::user()->name }}</li>
        <li>ユーザーID : {{ Auth::user()->id }}</li>
    </ul>

    <button><a href="{{ route('shop_register_form') }}">ショップ開設</a></button>

    <h2>ショップ一覧</h2>
    @if( $result == false )
        <p>店舗情報がありません。</p>
    @elseif( $result == true )
        <ul style="list-style: none;">
            @foreach( $user_shops as $shop )
                <li>
                    <a href="/shop/{{ $shop->shop_id }}/{{ $shop->shop_name }}">{{ $shop->shop_name }}</a>
                    <button>編集</button>
                    <button>削除</button>
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>