<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>coachtech</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner-mobile">
      <div class="nav-humberger">
        <input id="drawer_input" class="drawer_hidden" type="checkbox">
        <label for="drawer_input" class="drawer_open"><span></span></label>
        <nav class="nav_content">
          <ul class="nav_list">
            @if (Auth::check())
              <li class="nav_item">
                <a class="sell__link" href="/sell">出品</a>
              </li>
              <li class="nav_item">
                <a href="{{ url('mypage') }}">マイページ</a>
              </li>
              <li class="nav_item">
                <form action="/logout" method="post">
                  @csrf
                  <button class="logout">ログアウト</button>
                </form>
              </li>
            @else
              <li class="header-nav__white">
                <a class="header-item__link" href="/login">出品</a>
              </li>
              <li class="nav_item">
                <a href="{{ url('register') }}">会員登録</a>
              </li>
              <li class="nav_item">
                <a href="{{ url('login') }}">ログイン</a>
              </li>
            @endif
          </ul>
        </nav>
      </div>
      <a class="header__logo-mobile" href="/">
        <img class="logo-mobile" src="{{ asset('images/logo.svg') }}">
      </a>
      <button id="searchButton">
        <img class="search__logo" src="{{ asset('images/search.jpg') }}">
      </button>
      <div id="searchBar" class="search-bar">
        <form action="/search" class="search-word-hover" method="get">
          <input class="search__box" type="text" name="keyword" placeholder="なにをお探しですか?" value="{{ request('keyword') }}">
        </form>
      </div>
    </div>
    <form action="/search" class="search-word-mobile" method="get">
      <input class="search__box" type="text" name="keyword" placeholder="なにをお探しですか?" value="{{ request('keyword') }}">
    </form>
    <div class="header__inner">
      <div class="header-utilities">
        <a class="header__logo" href="/">
          <img src="{{ asset('images/logo.svg') }}">
        </a>
        <form action="/search" class="search-word" method="get">
          <input class="search__box" type="text" name="keyword" placeholder="なにをお探しですか?" value="{{ request('keyword') }}">
        </form>
        <nav>
          <ul class="header-nav">
          @if (Auth::check())
            <li class="header-nav__item">
              <form action="/logout" method="post">
                @csrf
                <button class="header-nav__button">ログアウト</button>
              </form>
            </li>
            <li class="header-nav__item">
              <a class="header-nav__link" href="/mypage">マイページ</a>
            </li>
          @else
            <li class="header-nav__item">
              <a class="header-nav__link" href="/login">ログイン</a>
            </li>
            <li class="header-nav__item">
              <a class="header-nav__link" href="/register">会員登録</a>
            </li>
          @endif
          @if (Auth::check())
            <li class="header-nav__white">
              <a class="header-item__link" href="/sell">出品</a>
            </li>
          @else
            <li class="header-nav__white">
              <a class="header-item__link" href="/login">出品</a>
            </li>
          @endif
          </ul>
        </nav>
      </div>
    </div>
  </header>
  @if (session('success'))
    <div class="alert-success">
      {{ session('success') }}
    </div>
  @elseif (session('alert'))
    <div class="alert-danger">
      {{ session('alert') }}
    </div>
  @endif
  <main>
    @yield('content')
  </main>
</body>

</html>