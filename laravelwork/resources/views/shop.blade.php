<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>{{ $shop->name }}</title>
</head>
<body>

    @include('parts.header')

    <main>


    <a href="{{ route('home') }}">トップページへ戻る</a>
    <h1>{{ $shop->name }}</h1>

        @if( $shop->image )
            <img class="image" src="{{ asset('storage/shop_images/' . $shop->image) }}">
        @elseif( is_null($shop->image) )
            <img class="image" src="{{ asset('storage/shop_images/no_image_logo.png') }}">
        @endif

        <p>{{ $shop->discription }}</p>

        <h2>商品一覧</h2>
        @if( Auth::user() && Auth::user()->id == $shop->user_id )
            <button><a href="/product_register_form/{{ $shop->id }}">商品登録</a></button>

            <form action="{{ route('csv_file') }}" method="POST">
            @csrf
                <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                <input type="hidden" name="shop_name" value="{{ $shop->name }}">
                <input type="hidden" name="product_exist_flag" value="{{ $result }}">
                <input type="submit" value="CSV出力">
            </form>
        @endif

        @if ( session('product_delete_success') )
            <p class="success">{{ session('product_delete_success') }}</p>
        @endif

        @if ( session('product_delete_err') )
            <p class="fail">{{ session('product_delete_err') }}</p>
        @endif

        @if ( session('product_register_success') )
            <p class="success">{{ session('product_register_success') }}</p>
        @endif

        @if ( session('product_edit_success') )
            <p class="success">{{ session('product_edit_success') }}</p>
        @endif

        @if ( session('csv_success') )
            <p class="success">{{ session('csv_success') }}</p>
        @endif

        @if ( session('csv_fail') )
            <p class="fail">{{ session('csv_fail') }}</p>
        @endif


        @if( $result == false )
        <p>商品情報がありません</p>
        @elseif( $result == true )
            <table border="0">
                @foreach($product_info as $product)
                    <tr>
                        <td><a href="/product/{{ $product->product_id }}/{{ $product->product_name }}">{{ $product->product_name }}</a></td>
                        @if( Auth::user() && Auth::user()->id == $shop->user_id )
                        <td>
                            <button><a href="/product_edit_form/{{ $product->product_id }}/{{ $product->product_name }}">編集</a></button>
                        </td>
                        <td>
                            <form action="{{ route('product_destroy') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                <input type="hidden" name="login_user" value="{{ Auth::user()->id }}">
                                <input type="submit" value="削除" onclick='return confirm("本当に削除しますか？")'>
                            </form>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif


     
    <a href="{{ route('home') }}">トップページへ戻る</a>

    </main>

</body>
</html>