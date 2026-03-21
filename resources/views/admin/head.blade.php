<head>
    @php
        $ProjectName = config('app.name', 'Suntulan');
    @endphp

    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <title>{{ $ProjectName }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('uploads/logo/Suntulan_logo.png') }}">


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">

    <!-- Font Awesome (use either local or CDN, not both) -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap/all.min.css') }}">
    <!-- jQuery UI -->
    <link rel="stylesheet" href="{{ asset('css/ui/1.13.1/themes/base/jquery-ui.css') }}">

    <!-- DataTables CSS (use either local or CDN, not both) -->
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.dataTables.min.css') }}">

    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    <!-- Main Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
