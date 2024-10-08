<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>MyShops</title>
</head>
<body>

    @include('parts.header')

    <main>


    <h1>MyShops</h1>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    <ul style="list-style: none;">
        <li><p style="font-weight: bold;">ログインユーザー</p></li>
        <li>ユーザー名 : {{ Auth::user()->name }}</li>
        <li>ユーザーID : {{ Auth::user()->id }}</li>
        <li>
            <form action="{{ route('user_edit_form') }}" method="GET">
            @csrf
                <input type="submit" value="ユーザー情報編集">
            </form>
        <li>

    </ul>


    <h2>ショップ一覧</h2>
    <button><a href="{{ route('shop_register_form') }}">ショップ開設</a></button>

    @if( session('shop_register_success') )
        <p class="success">{{ session('shop_register_success') }}</p>
    @endif

    @if( session('shop_edit_success') )
        <p class="success">{{ session('shop_edit_success') }}</p>
    @endif

    @if( session('shop_delete_success') )
        <p class="success">{{ session('shop_delete_success') }}</p>
    @endif
    
    @if( session('shop_delete_err') )
        <p class="fail">{{ session('shop_delete_err') }}</p>
    @endif

    @if( $result == false )
        <p>店舗情報がありません。</p>
    @elseif( $result == true )
        <table border="0">
            @foreach( $user_shops as $shop )
                <tr>
                    <td><a href="/shop/{{ $shop->shop_id }}/{{ $shop->shop_name }}">{{ $shop->shop_name }}</a></td>
                    @if( Auth::user() && Auth::user()->id == $shop->user_id )
                        <td>
                            <button><a href="{{ route('shop_edit_form', [$shop->shop_id, $shop->shop_name]) }}">編集</a></button>
                        </td>
                        <td>
                            <form action="{{ route('shop_destroy') }}" method="POST">
                                @csrf
                                    <input type="hidden" name="shop_id" value="{{ $shop->shop_id }}">
                                    <input type="hidden" name="login_user" value="{{ Auth::user()->id }}">
                                    <button type="submit" onclick='return confirm("本当に削除しますか？")'>削除</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    @endif

    <a href="{{ route('home') }}">トップページへ戻る</a>


    </main>

</body>
</html>