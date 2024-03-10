<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ショップ編集フォーム</title>
</head>
<body>
    <a href="{{ route('home') }}">トップページへ戻る</a>

    <h1>ショップ編集フォーム</h1>
    @if( session('shop_edit_err') ) 
        <p style="color: red;">{{ session('shop_edit_err') }}</p>
    @endif

    <form action="{{ route('shop_edit') }}" method="POST">
    @csrf
    <fieldset>
            <input type="hidden" name="login_user" value="{{ Auth::user()->id }}">
            <input type="hidden" name="shop_id" value="{{ $shop->id }}">

            <p>ショップ名</p>
            <input type="name" name="name" value="{{ $shop->name }}" title="ショップ名">
            @if ( $errors->has('name') )
                <p style="color: red;">{{ $errors->first('name') }}</p>
            @endif

            <p>自由記述欄</p>
            <textarea name="discription" rows="10" cols="100" title="ショップ概要">{{ $shop->discription }}</textarea><br>

            <input type="submit" value="ショップ情報を変更">
    </fieldset>
    </form>

    <a href="{{ route('home') }}">トップページへ戻る</a>


</body>
</html>