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
                        <li class="nav_item">
                            <a class="" href="">勤怠</a>
                        </li>
                        <li class="nav_item">
                            <a href="{{ url('') }}">勤怠一覧</a>
                        </li>
                        <li class="nav_item">
                            <a href="{{ url('') }}">申請</a>
                        </li>
                        <li class="nav_item">
                            <form action="/logout" method="post">
                                @csrf
                                <button type="submit" class="logout">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
            <a class="header__logo-mobile" href="/">
                <img class="logo-mobile" src="{{ asset('images/logo.svg') }}">
            </a>
        </div>
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    <img src="{{ asset('images/logo.svg') }}">
                </a>
                <nav>
                    <ul class="header-nav">
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="">勤怠</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="">勤怠一覧</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="">申請</a>
                        </li>
                        <li class="header-nav__item">
                            <form action="/logout" method="post">
                                @csrf
                                <button type="submit" class="header-nav__button">ログアウト</button>
                            </form>
                        </li>
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