@extends('layouts.guest')
@section('content')
    <style>
        @font-face {
            font-family: "Akkurat-Regular";
            src: url("../font/akkurat/lineto-akkurat-regular.eot");
            src: url("../font/akkurat/lineto-akkurat-regular.eot?#iefix") format("embedded-opentype"),
                url("../font/akkurat/lineto-akkurat-regular.woff") format("woff");
            font-weight: normal;
            font-style: normal;
        }

        .cf:before,
        .cf:after {
            content: " ";
            display: table;
        }

        .cf:after {
            clear: both;
        }

        * {
            box-sizing: border-box;
        }

        html {
            font-size: 16px;
            background-color: #000000;
        }

        body {
            padding: 0 20px;
            min-width: 300px;
            font-family: 'Akkurat-Regular', sans-serif;
            background-color: #000000;
            color: #1a1a1a;
            text-align: center;
            word-wrap: break-word;
            -webkit-font-smoothing: antialiased
        }

        a:link,
        a:visited {
            color: #00c2a8;
        }

        a:hover,
        a:active {
            color: #03a994;
        }


        .site-header {
            margin: 0 auto;
            padding: 80px 0 0;
            max-width: 820px;
        }

        .site-header__title {
            margin: 0;
            font-family: Montserrat, sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.1;
            text-transform: uppercase;
            -webkit-hyphens: auto;
            -moz-hyphens: auto;
            -ms-hyphens: auto;
            hyphens: auto;
            color: #ffffff;
        }


        .main-content {
            margin: 0 auto;
            max-width: 820px;
        }

        .main-content__checkmark {
            font-size: 4.0625rem;
            line-height: 1;
            color: #24b663;
        }

        .main-content__body {
            margin: 20px 0 0;
            font-size: 1rem;
            line-height: 1.4;
            color: #FFF;
        }



        .site-footer {
            margin: 0 auto;
            padding: 80px 0 25px;
            padding: 0;
            max-width: 820px;
        }

        .site-footer__fineprint {
            font-size: 0.9375rem;
            line-height: 1.3;
            font-weight: 300;
        }



        @media only screen and (min-width: 40em) {
            .site-header {
                padding-top: 150px;
            }

            .site-header__title {
                font-size: 6.25rem;
            }

            .main-content__checkmark {
                font-size: 9.75rem;
            }

            .main-content__body {
                font-size: 1.25rem;
            }

            .site-footer {
                padding: 145px 0 25px;
            }

            .site-footer__fineprint {
                font-size: 1.125rem;
            }
        }

        #checkmark {
            font-size: 8em;
        }
        .logoo img {
            height: 90px;
            width: 90px;
        }
    </style>

    <link href='https://fonts.googleapis.com/css?family=Lato:300,400|Montserrat:700' rel='stylesheet' type='text/css'>
    <style>
        @import url(//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css);
        @import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
    </style>
    {{-- <link rel="stylesheet" href="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/default_thank_you.css"> --}}
    <script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/jquery-1.9.1.min.js"></script>
    <script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/html5shiv.js"></script>

    <header class="site-header" id="header">
        <div class="d-flex justify-content-center py-4">
            <a href="{{ route('book.show') }}" class="logoo  d-flex align-items-center wuto">
                {{-- <img src="{{ asset('assets/theme/img/logo.png') }}" alt=""> --}}
                <img src="https://kahayfaqeer.org/assets/kahe-faqeer-white-1.png" alt="">

                <!-- <span class="d-none d-lg-block">{{ env('APP_NAME') ?? '' }}</span> -->
            </a>
        </div>
        <h1 class="site-header__title" data-lead-id="site-header-title">THANK YOU!</h1>
    </header>

    <div class="main-content">
        <i class="fa fa-check main-content__checkmark" id="checkmark"></i>
        <p class="main-content__body" data-lead-id="main-content-body">آپ کی درخواست کے لیے آپ کا شکریہ۔ ہمارا سسٹم اس وقت تمام اندراجات پر کارروائی کر رہا ہے۔ اگر سسٹم آپ کے ٹوکن کو منظور کرتا ہے تو یہ آپ کے واٹس ایپ پر ٹوکن کی تفصیلات بھیجے گا۔ برائے مہربانی مزید نئی اندراجات نہ کریں اور اگلے 2 گھنٹے انتظار کریں۔</p>
        <p class="main-content__body" data-lead-id="main-content-body">Thank you for your request. Our system is processing all entries at this time. If system approve your token then it will send token details to your WhatsApp. Kindly don't make further new entries and wait for the next 2 hours.</p>
    </div>
@endsection
