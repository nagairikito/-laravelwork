<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ショップ開設・登録</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>

    @if( session('register_shop_err') ) <!-- ショップ開設時にエラーが発生した場合のメッセージ -->
        <p style="color: red;">{{ session('register_shop_err') }}</p>
    @endif

    <form action="{{ route('shop_register') }}" method="POST">
    @csrf
        <p>ショップ名</p>
        <input type="name" name="name">
        @if ( $errors->has('name') )
            <p style="color: red;">{{ $errors->first('name') }}</p>
        @endif

        <p>自由記述欄</p>
        <textarea rows="10" cols="100"></textarea><br>

        <input type="submit" value="ショップ登録">
    </form>

    <a href="{{ route('home') }}">トップページへ戻る</a>

</body>
</html>