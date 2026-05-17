<!DOCTYPE html>
<html dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', @lang('site.msa')) }}</title> --}}
<title>@lang('site.atis')</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

        <link rel="shortcut icon" href="{{ asset('dashboard/images/logo-2.png') }}" type="image/png">


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/iziToast.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @if (app()->getLocale() == 'ar')
          <link rel="stylesheet" href="{{ asset('dashboard/css/font-awesome-rtl.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/AdminLTE-RTL.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/fontcairo.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/bootstrap-rtl.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/rtl.css') }}">

          <style>
            body, h1, h2, h3, h4, h5, h6{
              font-family: 'Cairo', sans-serif !important;
            }
            body, html {
                overflow: auto!important;
            }
            body {
                background-image: url('{{ asset("dashboard/images/login/background.jpg") }}')!important;
                background-size: cover!important;
                background-position: center center;
                background-repeat: no-repeat!important;
            }
          </style>
        @else
          <link rel="stylesheet" href="{{ asset('dashboard/css/AdminLTE.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/bootstrap.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/font-awesome.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/ionicons.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/fontbasic.css') }}">
          <style>
            body {
                background-image: url('{{ asset("dashboard/images/login/background.jpg") }}')!important;
                background-size: cover!important;
                background-position: center center;
                background-repeat: no-repeat!important;
            }
        </style>
        @endif

    <style>
        .login-box-body, .register-box-body {
            color: rgb(102, 102, 102);
            background: rgb(255, 255, 255);
            padding: 20px;
            border-top: 0px;
        }
        .has-feedback label~.form-control-feedback {
            top: 25px;
        }
    </style>
</head>
{{--
    background-image: url({{ asset('dashboard/images/login/1398313.jpg') }});
    background-size: cover!important;
    background-repeat: no-repeat!important;
    --}}
<body style="overflow: auto!important">
        {{--<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>--}}

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('dashboard/js/adminlte.min.js')}}"></script>
    <script src="{{ asset('dashboard/js/jquery.min.js')}}"></script>
    <script src="{{ asset('dashboard/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('dashboard/plugins/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('dashboard/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/iziToast.js') }}"></script>
    @include('vendor.lara-izitoast.toast')

    <script>
        $(function(){
            $(".doclogslid").on('click', function (e) {
                e.preventDefault();
                $(".logtext").html('تسجيل دخول كعضو هيئة تدريس')
            });

            $(".adlogslid").on('click', function (e) {
                e.preventDefault();
                $(".logtext").html('تسجيل دخول كموظف شئون طلاب')
            });
        });
    </script>

</body>
</html>
