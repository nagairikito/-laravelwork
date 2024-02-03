<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    <h2>{{ $product->name }}</h2>
    <p>販売元：{{ $shop_name }}</p>
        <h3>商品情報</h3>
        <p>{{ $product->discription }}</p>

    
    <a href="{{ route('home') }}">トップページへ戻る</a>

    
</body>
</html>