<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shop->name }}</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    <h2>{{ $shop->name }}</h2>
        <p>{{ $shop->discription }}</p>

        <h3>商品一覧</h3>
        <ul style="list-style: none;">
        @foreach($product_info as $product)
            <li>{{ $product->product_name }}</li>
        @endforeach
        </ul>


     
    <a href="{{ route('home') }}">トップページへ戻る</a>

    
</body>
</html>