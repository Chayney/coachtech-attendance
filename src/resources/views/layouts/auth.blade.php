<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>coachtech</title>
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header-utilities">
        <a class="header__logo" href="">
          <img src="{{ asset('images/logo.svg') }}">
        </a>
      </div>
    </div>
  </header>
  <main>
    @yield('content')
  </main>
</body>

</html>