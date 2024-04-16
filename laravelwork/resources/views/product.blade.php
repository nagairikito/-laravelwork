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

    @if( session('cart_success') )
        <p class="success">{{ session('cart_success') }}</p>
    @endif

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
            <form action="{{ route('add_shopping_cart') }}" method="POST">
            @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="name" value="{{ $product->name }}">
                <input type="hidden" name="price" value="{{ $product->price }}">
                <input type="hidden" name="image" value="{{ $product->image }}">
                <p>個数:<input type="number" name="num" value=1 min=1 max=99></p>
                <input type="submit" value="カートに入れる"><br><br>
            </form>

            <button><a href="/purchase_form/{{ $product->id }}/{{ $product->name }}">購入</a></button>

        @elseif( $product->stock == 0 )
            <button class="sold_out">SOLD OUT</button>
        @endif

        <br>
        <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>