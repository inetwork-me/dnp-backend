<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Styles -->
  <style>
    body {
      font-family: Arial, sans-serif;
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      background: url("{{ static_asset('assets') }}/img/login_bg.jpg") no-repeat center center/cover; /* Example background image */
    }

     /* Overlay */
     .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
      z-index: 1;
    }

    .login-container {
      width: 100%;
      max-width: 400px;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      background: linear-gradient(to right, #37143e, #4e1f5a); /* Gradient from dark purple to lighter purple */
    }

    .login-container h3 {
      color: white;
      text-align: center;
      margin-bottom: 20px;
    }

    .logo {
      display: block;
      margin: 0 auto 20px;
      max-width: 120px;
    }

    /* Label and input text color */
    .form-label {
      color: white;
    }

    .form-control {
      background-color: #4e1f5a;
      border-color: #6a2c7f;
      color: white;
    }

    .form-control:focus {
      background-color: #5a2278;
      border-color: #9b4d93;
      box-shadow: none;
    }

    .btn-custom {
      background-color: #9b4d93;
      color: white;
      border-radius: 5px;
      width: 100%;
      padding: 10px;
      font-size: 16px;
    }

    .btn-custom:hover {
      background-color: #7b3e6f;
    }

    .form-check-label {
      color: white;
    }

    .footer-text {
      text-align: center;
      margin-top: 20px;
      color: white;
    }
    
    input{
        color: white !important;
    }
    input::placeholder{
        color: rgba(155, 77, 147, 0.992) !important;
    }

    @media (max-width: 576px) {
      .login-container {
        padding: 20px;
      }
      .btn-custom {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
    
  <div class="login-container">
    <!-- Logo -->
    <img src="{{ uploaded_asset(get_setting('site_icon')) }}" alt="Logo" class="logo">

    <h3 class="mb-2 card-title">{{ translate('Login to your account') }}</h3>
    <p class="text-white text-center">{{ translate('Welcome to') }} {{ env('APP_NAME') }}</p>

    <!-- Login Form -->
    @yield('body')

  </div>



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
