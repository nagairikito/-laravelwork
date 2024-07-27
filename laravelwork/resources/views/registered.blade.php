<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>登録完了のお知らせ</title>
</head>
<body>

    @include('parts.header')

    <main>

    <h1>登録完了のお知らせ</h1>
    <p>登録が完了いたしました。</p>
    <a href="{{ route('home') }}">トップページへ戻る</a>

    </main>

</body>
</html>