<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <title>ご購入完了画面</title>
</head>
<body>

    @include('parts.header')

    <main>

    <h2>商品購入手続き完了のお知らせ</h2>
    <p>ご購入ありがとうございます。</p>
    <p>商品購入の手続きが完了いたしました。</p>

    @if( session('purchase_error') )
        <div class="fail">
            <p>尚、以下の商品の購入に失敗しました。</p>
            @foreach( $purchase_error as $key => $value )
                <p>{{ $value }}<p>
            @endforeach
        <div><br>
    @endif

    <a href="{{ route('home') }}">トップページへ戻る</a>
    
    </main>

</body>
</html>