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
    <p style="font-weight: bold;">ユーザー名 : {{ Auth::user()->name }}</p>
    <p style="font-weight: bold;">ユーザーID : {{ Auth::user()->id }}</p>
    

    @if( $result == false )
        <p>店舗情報がありません。</p>
    @elseif( $result == true )
        <ul style="list-style: none;">
            @foreach( $user_shops as $shop )
                <li><a href="/shop/{{ $shop->shop_id }}/{{ $shop->shop_name }}">{{ $shop->shop_name }}</a></li>
            @endforeach
        </ul>
    @endif
</body>
</html>