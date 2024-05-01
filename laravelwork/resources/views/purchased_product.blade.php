<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css"> リセットcss-->
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>購入履歴</title>
</head>
<body>

    <h2>購入履歴</h2>
    @if( isset( $purchased_products ) )
        <table border="1">
            @foreach( $purchased_products as $product => $value )
                <tr>
                    <td>
                        <a href="{{ route('product_detail', [ $value['id'], $value['name'] ]) }}"><img class="" style="width: 200px;" src="{{ asset('storage/product_images/' . $value['image']) }}"></a>
                    </td>
                    <td>
                        <p>{{ $value['name'] }}</p>
                        <p style="color: red;">￥{{ $value['price'] }}円</p>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p>購入履歴がありません。</p>
    @endif

    <br>
    <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>