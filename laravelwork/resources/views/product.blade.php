<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    <h1>{{ $product->name }}</h1>
    <img src="">
    <p style="color: red;">￥{{ $product->price }}円</p>
    <p>販売元：{{ $shop_name }}</p>
        <h2>商品情報</h2>
        <p>{{ $product->discription }}</p><br>
        
        <button><a href="/purchase_form/{{ $product->id }}/{{ $product->name }}">購入</a></button><br>
    
    <a href="{{ route('home') }}">トップページへ戻る</a>

    
</body>
</html>