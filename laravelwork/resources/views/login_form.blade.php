<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>ログイン画面</title>
</head>
<body>

    @include('parts.header')

    <main>

    <a href="{{ route('home') }}">トップページへ戻る</a>

    <h1>ログインフォーム</h1>
    
    @if( session('login_error') ) <!-- ログイン失敗時のエラーメッセージ -->
        <p class="fail">{{ session('login_error') }}</p>
    @endif

    @if( session('need_login') )
        <p class="fail">{{ session('need_login') }}</p>
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

    </main>

</body>
</html>