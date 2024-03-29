<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shop->name }}</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    <h1>{{ $shop->name }}</h1>
        <p>{{ $shop->discription }}</p>

        <h2>商品一覧</h2>
        @if( Auth::user() && Auth::user()->id == $shop->user_id )
            <button><a href="/product_register_form/{{ $shop->id }}">商品登録</a></button>
        @endif

        @if ( session('product_delete_success') )
            <p style="color: green">{{ session('product_delete_success') }}</p>
        @endif

        @if ( session('product_delete_err') )
            <p style="color: red">{{ session('product_delete_err') }}</p>
        @endif

        @if ( session('product_register_success') )
            <p style="color: green">{{ session('product_register_success') }}</p>
        @endif

        @if ( session('product_edit_success') )
            <p style="color: green">{{ session('product_edit_success') }}</p>
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
                                    <alert><input type="submit" value="削除"></alert>
                                </form>
                            </td>
                            @endif
                    </tr>
                @endforeach
            </table>
        @endif


     
    <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>