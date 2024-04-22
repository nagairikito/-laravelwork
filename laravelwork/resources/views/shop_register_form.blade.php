<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>ショップ開設・登録フォーム</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>

    <h1>ショップ開設・登録フォーム</h1>
    @if( session('shop_register_err') ) <!-- ショップ開設時にエラーが発生した場合のメッセージ -->
        <p class="fail">{{ session('shop_register_err') }}</p>
    @endif

    <form action="{{ route('shop_register') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        <p>ショップ名</p>
        <input type="name" name="name">
        @if ( $errors->has('name') )
            <p calss="fail">{{ $errors->first('name') }}</p>
        @endif

        <p>自由記述欄</p>
        <textarea name="discription" rows="10" cols="100"></textarea><br>

        <p>ショップの画像</p>
        <input type="file" name="image"><br>

        <input type="submit" value="ショップ登録" onclick='return confirm("本当にショップ登録してよろしいですか？")'>
    </form>

    <a href="{{ route('home') }}">トップページへ戻る</a>

</body>
</html>