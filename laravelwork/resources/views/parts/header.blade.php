<header>
        <form class="search_var" action="{{ route('search') }}" method="POST">
        @csrf
            <input type="search" name="keyword" placeholder="商品名・ショップ名" style="width: 80%; height: 100%;">
            <input type="submit" value="検索" style="width: 15%; height: 100%;">
        </form>

        @if ( is_null(Auth::user()) )
                <p class="header_nav"><a href="{{ route('login_form') }}">ログイン</a></p>
        @endif

        @if ( Auth::user() )
            <ul class="header_nav_auth">
                <li><a href="{{ route('login_form') }}" >アカウント切り替え</a></li>
                <li><a href="{{ route('shop_orner', [ Auth::user()->id ]) }}">ショップオーナー</a></li>
                <li><a href="{{ route('favorite_product', [ Auth::user()->id ]) }}"><span style="font-size: 1.4em;">☆</span>お気に入り</a></li>
                <li><a href="{{ route('shopping_cart', [ Auth::user()->id, Auth::user()->name ]) }}">🛒買い物カゴ</a></li>
                <li><a href="{{ route('purchased_product', [ Auth::user()->id, Auth::user()->name ]) }}">🌐購入履歴</a></li>
            </ul>
        @endif
</header>
<div class="under_header">header</div>


