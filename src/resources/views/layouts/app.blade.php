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
                        @if (Auth::user()->hasRole('admin'))
                            <li class="nav_item">
                                <a href="{{ url('/admin/attendance/list') }}">勤怠一覧</a>
                            </li>
                            <li class="nav_item">
                                <a href="{{ url('/admin/staff/list') }}">スタッフ一覧</a>
                            </li>
                            <li class="nav_item">
                                <a href="{{ url('/stamp_correction_request/list') }}">申請一覧</a>
                            </li>
                        @else
                            <li class="nav_item">
                                <a href="{{ url('/attendance') }}">勤怠</a>
                            </li>
                            <li class="nav_item">
                                <a href="{{ url('/attendance/list') }}">勤怠一覧</a>
                            </li>
                            <li class="nav_item">
                                <a href="{{ url('/stamp_correction_request/list') }}">申請</a>
                            </li>
                        @endif
                        <li class="nav_item">
                            <form action="/logout" method="post">
                                @csrf
                                <button type="submit" class="logout">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
            @if (Auth::user()->hasRole('admin'))
                <a class="header__logo-mobile" href="/admin/attendance/list">
                    <img class="logo-mobile" src="{{ asset('images/logo.svg') }}">
                </a>
            @else
                <a class="header__logo-mobile" href="/attendance">
                    <img class="logo-mobile" src="{{ asset('images/logo.svg') }}">
                </a>
            @endif
        </div>
        <div class="header__inner">
            <div class="header-utilities">
                @if (Auth::user()->hasRole('admin'))
                    <a class="header__logo" href="/admin/attendance/list">
                        <img src="{{ asset('images/logo.svg') }}">
                    </a>
                @else
                    <a class="header__logo" href="/attendance">
                        <img src="{{ asset('images/logo.svg') }}">
                    </a>
                @endif
                <nav>
                    <ul class="header-nav">
                        @if (Auth::user()->hasRole('admin'))
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="{{ url('/admin/attendance/list') }}">勤怠一覧</a>
                            </li>
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="{{ url('/admin/staff/list') }}">スタッフ一覧</a>
                            </li>
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="{{ url('/stamp_correction_request/list') }}">申請一覧</a>
                            </li>
                        @else
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="{{ url('/attendance') }}">勤怠</a>
                            </li>
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="{{ url('/attendance/list') }}">勤怠一覧</a>
                            </li>
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="{{ url('/stamp_correction_request/list') }}">申請</a>
                            </li>
                        @endif
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