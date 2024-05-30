<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>kahayfaqeer.org</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.0.0/mdb.min.css" rel="stylesheet" />
    <!-- Favicons -->
    <link href="{{ asset('assets/theme/img/logo.png') }}" type="image/x-icon">
    <link href="{{ asset('assets/theme/img/logo.png') }}" rel="icon">
    <link href="{{ asset('assets/theme/img/logo.png') }} " rel="apple-touch-icon">
    <link href="{{ asset('assets/fonts/Jameel-Noori-Nastaleeq-Regular.ttf') }}" rel="stylesheet" />
    <!-- Google Fonts -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link href="{{ asset('assets/theme/css/custom.css') }}" rel="stylesheet">
    <style type="text/css">
        .logoo img {
            height: 140px;
            width: 150px;
        }
    </style>

    @if(request()->RouteIs('book.show'))
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-JKTQMG8C33"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-JKTQMG8C33');
        </script>
    @endif



</head>

<body>
