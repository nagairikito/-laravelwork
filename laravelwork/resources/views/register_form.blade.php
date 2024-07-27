<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>新規登録フォーム</title>
</head>
<body>

    @include('parts.header')

    <main>

    <a href="{{ route('home') }}">トップページへ戻る</a>

    <h1>新規登録フォーム</h1>

    <form action="{{ route('register') }}" method="POST">
    @csrf
        <p>ご氏名</p>
        <input type="name" name="name">
        @if ( $errors->has('name') )
            <p style="color: red;">{{ $errors->first('name') }}</p>
        @endif

        <p>メールアドレス</p>
        <input type="email" name="email">
        @if ( $errors->has('email') )
            <p style="color: red;">{{ $errors->first('email') }}</p>
        @endif

        <p>パスワード</p>
        <input type="password" name="password">
        @if ( $errors->has('password') )
            <p style="color: red;">{{ $errors->first('password') }}</p>
        @endif

        <p>確認用パスワード</p>
        <input type="password" name="password_confirmation"><br>
        @if ( $errors->has('password.confirmed') )
            <p style="color: red;">{{ $errors->first('password.confirmed') }}</p>
        @endif

        <input type="submit" value="新規登録">
    </form>

    <a href="{{ route('home') }}">トップページへ戻る</a>

    </main>

</body>
</html>