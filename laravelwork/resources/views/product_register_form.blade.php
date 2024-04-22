<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>商品登録フォーム</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>
    
    <h1>商品登録フォーム</h1>

    @if( session('product_register_err') ) 
        <p style="color: red;">{{ session('product_register_err') }}</p>
    @endif

    <form action="{{ route('product_register') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="shop_id" value="{{ $shop_id }}">
        <p>商品名</p>
        <input type="name" name="name">
        @if ( $errors->has('name') )
            <p style="color: red;">{{ $errors->first('name') }}</p>
        @endif

        <p>設定金額</p>
        <input type="number" name="price" min=0>
        @if ( $errors->has('price') )
            <p style="color: red;">{{ $errors->first('price') }}</p>
        @endif

        <p>在庫数</p>
        <input type="number" name="stock" min=0>
        <p>※商品の登録のみ行う場合は在庫数を0に設定してください。</p>
        @if ( $errors->has('stock') )
            <p style="color: red;">{{ $errors->first('stock') }}</p>
        @endif

        <p>自由記述欄</p>
        <textarea name="discription" rows="10" cols="100"></textarea><br>

        <p>商品の画像</p>
        <input type="file" name="image"><br>

        <input type="submit" value="商品登録" onclick='return confirm("本当に商品登録してよろしいですか？")'>
    </form>

    <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>