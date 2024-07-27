<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>商品編集フォーム</title>
</head>
<body>

    @include('parts.header')
    
    <main>

    <a href="{{ route('home') }}">トップページへ戻る</a>
    
    <h1>商品編集フォーム</h1>


    @if( session('product_edit_err') ) 
        <p style="color: red;">{{ session('product_edit_err') }}</p>
    @endif

    <form action="{{ route('product_edit') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="login_user" value="{{ Auth::user()->id }}">
        <input type="hidden" name="product_id" value="{{ $product_info->id }}">
        <input type="hidden" name="shop_id" value="{{ $product_info->shop_id }}">
        <p>商品名</p>
        <input type="name" name="name" value="{{ $product_info->name }}">
        @if ( $errors->has('name') )
            <p style="color: red;">{{ $errors->first('name') }}</p>
        @endif

        <p>設定金額</p>
        <input type="number" name="price" min=0 value="{{ $product_info->price }}">
        @if ( $errors->has('price') )
            <p style="color: red;">{{ $errors->first('price') }}</p>
        @endif

        <p>在庫数</p>
        <input type="number" name="stock" min=0 value="{{ $product_info->stock }}">
        <p>※商品の登録のみ行う場合は在庫数を0に設定してください。</p>
        @if ( $errors->has('stock') )
            <p style="color: red;">{{ $errors->first('stock') }}</p>
        @endif

        <p>自由記述欄</p>
        <textarea name="discription" rows="10" cols="100">{{ $product_info->discription }}</textarea><br>

        <input type="file" name="image"><br>

        <input type="submit" value="商品情報を更新" onclick='return confirm("本当に変更してよろしいですか？")'>
    </form>


    <a href="{{ route('home') }}">トップページへ戻る</a>

    </main>


</body>
</html>