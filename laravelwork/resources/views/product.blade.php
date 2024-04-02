<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>{{ $product->name }}</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    <h1>{{ $product->name }}</h1>

    @if( $product->image )
        <img class="image" src="{{ asset('storage/product_images/' . $product->image) }}">
    @elseif( is_null($product->image) )
        <img class="image" src="{{ asset('storage/product_images/no_image_logo.png') }}">
    @endif

    @if( $product->stock == 0 )
        <p class="fail">SOLD OUT</p>
    @endif

    <p style="color: red;">￥{{ $product->price }}円</p>
    <p>販売元：{{ $shop_name }}</p>
        <h2>商品情報</h2>
        <p>{{ $product->discription }}</p><br>
        
        @if( $product->stock > 0 )
            <button><a href="/purchase_form/{{ $product->id }}/{{ $product->name }}">購入</a></button><br>
        @elseif( $product->stock == 0 )
            <button class="sold_out">SOLD OUT</button>
        @endif

        <br>
        <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>