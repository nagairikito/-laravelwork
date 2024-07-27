<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css"> リセットcss-->
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>{{ $keyword }}</title>
</head>
<body>

    @include('parts.header')

    <main>


    <form class="" action="{{ route('search') }}" method="POST">
    @csrf
        <input type="search" name="keyword" placeholder="商品名・ショップ名" value="{{ $keyword }}" style="width: 15%; height: 100%;">
        <input type="submit" value="検索" style="width: 15%; height: 100%;">
    </form>

    
    @if( $result_count > 0 )
        <p>検索結果： 該当商品　{{ $result_count }} 件</p>
        <table border="1">
            @foreach( $result as $product => $value )
                <tr>
                    <td>
                        <a href="{{ route('product_detail', [ $value['id'], $value['name'] ]) }}"><img class="" style="width: 200px;" src="{{ asset('storage/product_images/' . $value['image']) }}"></a>
                    </td>
                    <td>
                        <a href="{{ route('product_detail', [ $value['id'], $value['name'] ]) }}">
                            <p>{{ $value['name'] }}</p>
                            <p style="color: red;">￥{{ $value['price'] }}円</p>
                            <p>{{ $value['shop_name'] }}</p>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        {{ $result->links() }}

    @else
        <p>該当する商品がありません。</p>
    @endif

    <a href="{{ route('home') }}">トップページへ戻る</a>

    </main>

</body>
</html>