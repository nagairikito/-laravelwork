<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>ショッピングカート</title>
</head>
<body>
    <h1>ショッピングカート</h1>

    @if( session('delete_shopping_cart_success') )
        <p class="success">{{ session('delete_shopping_cart_success') }}</p>
    @endif

    @if( session('delete_shopping_cart_failed') )
        <p class="fail">{{ session('delete_shopping_cart_failed') }}</p>
    @endif

    @if( isset($shopping_cart_info) )
        <p style="color: red; font-weight: bold; font-size: 1.2rem;">合計金額　　￥{{ $shopping_cart_info['total_price'] }}円</p>
        <p style="color: red; font-weight: bold; font-size: 1.2rem;">カート内商品　　{{ $shopping_cart_info['total_count'] }}点</p>
    @endif

    @if( isset( $shopping_cart ) )
        <form action="{{ route('delete_all_shopping_cart') }}" method="POST">
        @csrf
            <input type="submit" value="カート内の商品を空にする">

        </form>
        <table border="1">
            @foreach( $shopping_cart as $product => $value )
                <tr>
                    <td>
                        <a href="{{ route('product_detail', [ $value['id'], $value['name'] ]) }}"><img class="" style="width: 200px;" src="{{ asset('storage/product_images/' . $value['image']) }}"></a>
                    </td>
                    <td>
                        <p>{{ $value['name'] }}</p>
                        <p style="color: red;">￥{{ $value['price'] }}円</p>
                        <p>個数：<input type="number" min=1 max=99 value="{{ $value['num'] }}" style="width: 50px;">点</p>
                        <form action="{{ route('delete_shopping_cart') }}" method="POST">
                        @csrf
                            <input type="hidden" name="session_product_id" value="{{ $value['id'] }}">
                            <input type="submit" value="削除">
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p>カートに商品が入っていません。</p>
    @endif


    <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>