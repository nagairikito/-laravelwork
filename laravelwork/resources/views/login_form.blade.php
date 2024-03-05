<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>

    <h1>ログインフォーム</h1>

    @if ( session('login_error') ) <!-- ログイン失敗時のエラーメッセージ -->
        <p style="color: red;">{{ session('login_error') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
    @csrf
        <p>メールアドレス</p>
        <input type="email" name="email">
        @if ( $errors->has('email') )
            <p style="color: red;">{{ $errors->first('email') }}</p>
        @endif

        <p>パスワード</p>
        <input type="password" name="password"><br>
        @if ( $errors->has('password') )
            <p style="color: red;">{{ $errors->first('password') }}</p>
        @endif

        <input type="submit" value="ログイン">
    </form>

    <a href="{{ route('home') }}">トップページへ戻る</a>

</body>
</html>