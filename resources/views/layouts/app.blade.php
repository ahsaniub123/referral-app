<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>21 Spirit Premium</title>
    <meta property="og:title" content="21 Spirit Premium" />
    <meta property="og:url" content="https://account.21spirit.com/register" />
    <meta property="og:image" content="https://cdn.shopify.com/s/files/1/0550/4060/6257/files/21spiritad.jpg?v=1671564865"/>
    
    <link rel="shortcut icon" href="//cdn.shopify.com/s/files/1/0550/4060/6257/files/favicon-1_96x.png?v=1672810784" type="image/png">
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="{{ asset('admin/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href={{ asset('admin/css/tabler-vendors.min.css') }} rel="stylesheet"/>
    <link href="{{ asset('admin/css/demo.min.css') }}" rel="stylesheet"/>
</head>
<body class="antialiased border-top-wide border-primary d-flex flex-column">
    @yield('content')

    @include('managers.inc.footer')
</body>
</html>
