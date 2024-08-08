<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css"> リセットcss-->
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>お気に入り商品</title>
</head>
<body>

    @include('parts.header')

    <main>

    <h1>お気に入り商品</h1>

    @if( session('delete_favorite_product_success') )
        <p class="success">{{ session('delete_favorite_product_success') }}</p>
    @endif

    @if( session('delete_favorite_product_failed') )
        <p class="fail">{{ session('delete_favorite_product_failed') }}</p>
    @endif

    @if( count( $favorite_products ) > 0 )
        <form action="{{ route('delete_all_favorite_product') }}" method="POST">
        @csrf
            <input type="submit" value="お気に入りを空にする">
        </form>

            <table border="1">
                @foreach( $favorite_products as $product => $value )
                    <tr>
                        <td>
                            <a href="{{ route('product_detail', [ $value['id'], $value['name'] ]) }}"><img class="" style="width: 200px;" src="{{ asset('storage/product_images/' . $value['image']) }}"></a>
                        </td>
                        <td>
                            <p>{{ $value['name'] }}</p>
                            <p style="color: red;">￥{{ $value['price'] }}円</p>

                            <form action="{{ route('delete_favorite_product') }}" method="POST">
                            @csrf
                                <input type="hidden" name="session_favorite_product_id" value="{{ $value['id'] }}">
                                <input type="submit" value="削除">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>

    @else
        <p>お気に入りの商品がありません。</p>
    @endif

    <br>
    <a href="{{ route('home') }}">トップページへ戻る</a>

    </main>

</body>
</html>