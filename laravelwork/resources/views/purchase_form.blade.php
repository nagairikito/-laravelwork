<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品購入画面</title>
</head>
<body>
    <p>{{ $product->name }}</p>
    <img src="">
    <p style="color: red;">￥{{ $product->price }}円</p>

    @if( session('stock_error') )
        <p style="color: red;">{{ session('stock_error') }}</p>
    @endif

    <form action="{{ route('purchase') }}" method="POST">
    @csrf
        <input type="hidden" name="id" value="{{ $product->id }}">
        <p>個数:<input type="number" name="number_sold" value=0 min=0 max=99></p><br>
        <input type="submit" value="購入を確定する">
    </form>

    <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>