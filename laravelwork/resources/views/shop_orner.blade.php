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


    <h2>ショップ一覧</h2>
    <button><a href="{{ route('shop_register_form') }}">ショップ開設</a></button>
    @if( $result == false )
        <p>店舗情報がありません。</p>
    @elseif( $result == true )
        <table border="0">
            @foreach( $user_shops as $shop )
                <tr>
                    <td><a href="/shop/{{ $shop->shop_id }}/{{ $shop->shop_name }}">{{ $shop->shop_name }}</a></td>
                    @if( Auth::user() && Auth::user()->id == $shop->user_id )
                        <td>
                            <form action="{{ route('shop_edit') }}" method="POST">
                                <input type="hidden" value="{{ $shop->id }}">
                                <button type="submit">編集</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('shop_destroy') }}" method="POST">
                                <input type="hidden" value="{{ $shop->id }}">
                                <button type="submit">削除</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    @endif

    <a href="{{ route('home') }}">トップページへ戻る</a>



</body>
</html>