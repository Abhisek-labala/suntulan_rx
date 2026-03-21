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
</head>
