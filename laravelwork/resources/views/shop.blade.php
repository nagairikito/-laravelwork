<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shop->name }}</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    <h1>{{ $shop->name }}</h1>
        <p>{{ $shop->discription }}</p>

        <h2>商品一覧</h2>
        @if( Auth::user() && Auth::user()->id == $shop->user_id )
            <button><a href="/product_register_form/{{ $shop->id }}">商品登録</a></button>
        @endif

        @if( $result == false )
        <p>商品情報がありません</p>
        @elseif( $result == true )
            <table border="0">
                @foreach($product_info as $product)
                    <tr>
                        <td><a href="/product/{{ $product->product_id }}">{{ $product->product_name }}</a></td>
                            @if( Auth::user() && Auth::user()->id == $shop->user_id )
                            <td>
                                <form action="{{ route('product_edit') }}" method="POST">
                                    <input type="hidden" value="{{ $product->id }}">
                                    <button type="submit">編集</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('product_destroy') }}" method="POST">
                                    <input type="hidden" value="{{ $product->id }}">
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