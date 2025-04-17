<!DOCTYPE html>
<html class="loading" lang="en">
<!-- BEGIN : Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <link rel="apple-touch-icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link
        href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900%7CMontserrat:300,400,500,600,700,800,900"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/vendors/css/switchery.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/colors.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/components.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/themes/layout-dark.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/plugins/switchery.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/pages/authentication.css">
</head>
<!-- END : Head-->

<!-- BEGIN : Body-->

<body class="vertical-layout vertical-menu 1-column auth-page navbar-sticky blank-page" data-menu="vertical-menu"
    data-col="1-column">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="wrapper">
        <div class="main-panel">
            <!-- BEGIN : Main Content-->
            <div class="main-content">
                <div class="content-overlay"></div>
                <div class="content-wrapper">
                    <!--Login Page Starts-->
                    <section id="login" class="auth-height">
                        <div class="row full-height-vh m-0">
                            <div class="col-12 d-flex align-items-center justify-content-center">
                                <div class="card overflow-hidden">
                                    <div class="card-content">
                                        <div class="card-body auth-img">
                                            @yield('content')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!--Login Page Ends-->
                </div>
            </div>
            <!-- END : End Main Content-->
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <script src="{{ asset('assets') }}/vendors/js/vendors.min.js"></script>
    <script src="{{ asset('assets') }}/vendors/js/switchery.min.js"></script>
    <script src="{{ asset('assets') }}/js/core/app-menu.js"></script>
    <script src="{{ asset('assets') }}/js/core/app.js"></script>
    <script src="{{ asset('assets') }}/js/notification-sidebar.js"></script>
    <script src="{{ asset('assets') }}/js/customizer.js"></script>
    <script src="{{ asset('assets') }}/js/scroll-top.js"></script>
</body>
<!-- END : Body-->

</html>
