{{-- filepath: resources/views/header.blade.php --}}
<head>
    @php
        $ProjectName = config('app.name', 'Suntulan');
    @endphp

    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <title>{{ $ProjectName }}</title>

    <!-- Favicon or Logo -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('uploads/logo/Suntulan_logo.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap/all.min.css') }}">

    <!-- Feather Icons -->
    <!-- <link rel="stylesheet" href="{{ asset('css/feathericon.min.css') }}"> -->

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        /* Footer Credit */
.footer-credit {
    position: fixed;
    bottom: 12px;
    right: 20px;
    z-index: 9999;
    font-size: 11px;
    color: #777;
    font-weight: 400;
    font-family: 'Montserrat', sans-serif;
}

.footer-credit a {
    color: #AE3B26;
    text-decoration: none;
    font-weight: 600;
}

.footer-credit a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .footer-credit {
        right: 10px;
        bottom: 8px;
        font-size: 10px;
    }
}
    </style>
</head>
