<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ translate('Admin') }} || @yield('title')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">
    <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <link rel="apple-touch-icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f6f2f9;
            height: 100vh;
            margin: 0;
        }

        .navbar-custom {
            background-color: #ffffff;
            height: 80px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .navbar-custom .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .login-container {
            height: calc(100vh - 80px);
        }

        .login-box {
            max-width: 456px;
            width: 100%;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 5px;
        }

        .login-subtitle {
            font-size: 18px;
            color: #4B5563;
            margin-bottom: 20px;
        }

        .btn-login {
            background-color: #724582;
            color: white;
        }

        .btn-login:hover {
            background-color: #5f3570;
        }

        .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
            background-color: #724582;
            border-color: #724582;
        }

        .forgot-password {
            display: block;
            margin-top: 18px;
            color: #724582;
            text-decoration: underline;
            text-align: center;
            font-weight: 500;
        }

        /* Floating Labels */
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .floating-label input {
            height: 45px;
            padding: 10px 10px 10px 10px;
        }

        .floating-label label {
            position: absolute;
            top: 10px;
            left: 12px;
            color: #888;
            font-size: 14px;
            background: white;
            padding: 0 5px;
            transition: 0.2s;
            pointer-events: none;
            opacity: 0;
            transform: translateY(10px);
        }

        .floating-label input:focus+label,
        .floating-label input:not(:placeholder-shown)+label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            opacity: 1;
            transform: translateY(0);
            color: #724582;
        }

        .site_name {
            font-size: 20px;
            font-weight: 600;
            color: #4B5563;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-custom px-4">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ uploaded_asset(get_setting('system_logo_black')) }}" alt="Logo">
            <span class="ml-2 site_name">{{ get_setting('site_name') }}</span>
        </a>
    </nav>

    @yield('content')

    <!-- Bootstrap 4 JS dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.form-control').on('click', function() {
                $(this).attr('placeholder', '');
            }).on('blur', function() {
                if (!$(this).val()) {
                    $(this).attr('placeholder', $(this).siblings('label').text());
                }
            });
        });
    </script>
</body>

</html>
