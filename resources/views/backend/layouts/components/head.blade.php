<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">
    <title>{{ translate('Admin') }} || @yield('title')</title>
    <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <link rel="apple-touch-icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link
        href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900"
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
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/style.css">
    <!-- END: Custom CSS-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('css')
</head>
