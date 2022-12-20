<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta3
* @link https://tabler.io
* Copyright 2018-2021 The Tabler Authors
* Copyright 2018-2021 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>21 Spirit Premium</title>
    <meta property="og:title" content="21 Spirit Premium" />
    <meta property="og:url" content="https://account.21spirit.com/register" />
    <meta property="og:image" content="https://cdn.shopify.com/s/files/1/0550/4060/6257/files/21spiritad.jpg?v=1671564865"/>
    <!-- CSS files -->
    <link href="{{ asset('admin/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href={{ asset('admin/css/tabler-vendors.min.css') }} rel="stylesheet"/>
    <link href="{{ asset('admin/css/demo.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @yield('styles')
</head>
<body class="antialiased">
<div class="wrapper">
    @include('managers.inc.header')
    <div class="page-wrapper">
        <div class="container-xl">
            @yield('header')
        </div>
        <div class="page-body">
            <div class="container-xl">
                @yield('content')
            </div>
        </div>
        <footer class="footer footer-transparent d-print-none">
            <div class="container">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                Copyright &copy; 2022
                                <span href="#" class="link-secondary">{{ config('app.name') }}</span>.
                                All rights reserved.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

@include('managers.inc.footer')
@yield('script')
</body>
</html>
