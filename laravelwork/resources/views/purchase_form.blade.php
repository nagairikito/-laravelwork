<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>商品購入画面</title>
</head>
<body>
    <p>{{ $product->name }}</p>

    @if( session('cart_success') )
        <p class="success">{{ session('cart_success') }}</p>
    @endif

    @if( $product->image )
        <img src="{{ asset('storage/product_images/' . $product->image) }}">
    @elseif( is_null($product->image) )
        <img src="{{ asset('storage/product_images/no_image_logo.png') }}">
    @endif
    
    <p style="color: red;">￥{{ $product->price }}円</p>

    @if( session('stock_error') )
        <p style="color: red;">{{ session('stock_error') }}</p>
    @endif

    <form action="{{ route('purchase') }}" method="POST">
    @csrf
        <input type="hidden" name="id" value="{{ $product->id }}">
        <p>個数:<input type="number" name="number_sold" value=1 min=1 max=99></p><br>
        <input type="submit" value="購入を確定する">
    </form>

    <form action="{{ route('add_shopping_cart') }}" method="POST">
    @csrf
        <input type="hidden" name="id" value="{{ $product->id }}">
        <input type="hidden" name="name" value="{{ $product->name }}">
        <input type="hidden" name="price" value="{{ $product->price }}">
        <input type="hidden" name="image" value="{{ $product->image }}">
        <p>個数:<input type="number" name="num" value=1 min=1 max=99></p><br>
        <input type="submit" value="カートに入れる">
    </form>


    <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>