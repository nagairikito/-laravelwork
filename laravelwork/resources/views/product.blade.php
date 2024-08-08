<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>{{ $product->name }}</title>
</head>
<body>

    @include('parts.header')

    <main>

    <a href="{{ route('home') }}">トップページへ戻る</a>

    @if( session('favorite_success') )
        <p class="success">{{ session('favorite_success') }}</p>
    @endif

    @if( session('delete_favorite_product_success') )
        <p class="success">{{ session('delete_favorite_product_success') }}</p>
    @endif

    @if( session('cart_success') )
        <p class="success">{{ session('cart_success') }}</p>
    @endif

    <h1>{{ $product->name }}</h1>

    @if( $product->image )
        <img class="image" src="{{ asset('storage/product_images/' . $product->image) }}">
    @elseif( is_null($product->image) )
        <img class="image" src="{{ asset('storage/product_images/no_image_logo.png') }}">
    @endif

    @if( $favorite_flag == false )
        <form action="{{ route('add_favorite_product') }}" method="POST">
        @csrf
            <input type="hidden" name="id" value="{{ $product->id }}">
            <input type="submit" value="☆" class="favorite_product_button">
        </form>
    @else
        <form action="{{ route('delete_favorite_product') }}" method="POST">
        @csrf
            <input type="hidden" name="session_favorite_product_id" value="{{ $product->id }}">
            <input type="submit" value="★" class="favorite_product_button">
        </form>
    @endif

    @if( 0 < $product->stock && $product->stock <= 10 )
        <p class="important">残り{{ $product->stock }}点</p>
    @elseif( $product->stock == 0 )
        <p class="fail">SOLD OUT</p>
    @endif

    <p style="color: red;">￥{{ $product->price }}円</p>
    <p>販売元：{{ $shop->name }}</p>
        <h2>商品情報</h2>
        <p>{{ $product->discription }}</p><br>

        @if( Auth::user() )
            @if( $product->stock > 0 && $shop->user_id !== Auth::user()->id )
                <form action="{{ route('add_shopping_cart') }}" method="POST">
                @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="price" value="{{ $product->price }}">
                    <input type="hidden" name="image" value="{{ $product->image }}">
                    <p>個数:<input type="number" name="num" value=1 min=1 max=99></p>
                    <input type="submit" name="incart" value="カートに入れる"><br><br>
                </form>

                <button><a href="/purchase_form/{{ $product->id }}/{{ $product->name }}">購入</a></button>

            @elseif( $product->stock == 0 )
                <button class="sold_out">SOLD OUT</button>
            @elseif( $shop->user_id == Auth::user()->id )
                <button class="sold_out">購入</button>
                <p class="fail">自身の商品は購入できません。</p>
            @endif
        @else
                <form action="{{ route('login_form') }}" method="GET">
                @csrf
                    <p>個数:<input type="number" value=1 min=1 max=99></p>
                    <input type="submit" value="カートに入れる"><br><br>
                </form>

                <button><a href="{{ route('login_form') }}">購入</a></button>


        @endif

        <br>
        <a href="{{ route('home') }}">トップページへ戻る</a>

        </main>

</body>
</html>