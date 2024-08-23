<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>アカウント編集フォーム</title>
</head>
<body>

    @include('parts.header')

    <main>


    <h1>アカウント編集フォーム</h1>
    <form action="{{ route('user_edit') }}" method="POST">
    @csrf
        <p>氏名</p>
        <input type="hidden" name="login_user" value="{{ Auth::user()->id }}">

        <input type="name" name="name" value="{{ $user->name }}">
        @if ( $errors->has('name') )
        <p class="fail">{{ $errors->first('name') }}</p>
        @endif

        <p>メールアドレス</p>
        <p>{{ $user->email }}</p>

        <p>新しいパスワード</p>
        <input type="password" name="new_password">
        @if ( session('new_password_confirm_err') )
        <p class="fail">{{ session('new_password_confirm_err') }}</p>
        @endif
        @if ( session('new_password_err') )
        <p class="fail">{{ session('new_password_err') }}</p>
        @endif


        <p>新しいパスワードを再度ご入力ください</p>
        <input type="password" name="confirmation_new_password"><br>

        <input type="submit" value="ユーザー情報を変更する">
    </form>


    <br>
    <a href="{{ route('home') }}">トップページへ戻る</a>

</main>
</body>
</html>