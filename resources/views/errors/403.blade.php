@extends('layouts.guest')
@section('content')
<style>.error-404 h1 {
    font-size: 180px;
    font-weight: 700;
    color: #4154f1;
    margin-bottom: 0;
    line-height: 150px;
}
@media (min-width: 992px) {
    .error-404 img {
        max-width: 20%;
    }
}
.error-404 .btn {
    background: #51678f;
    color: #fff;
    padding: 8px 30px;
}
</style>
<section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
    <h1>403</h1>
    <h2>This website you can access only via mobile. Please open this website from your mobile phone.</h2>

    <h2>اس ویب سائٹ تک آپ صرف موبائل کے ذریعے ہی رسائی حاصل کر سکتے ہیں۔ براہ کرم اس ویب سائٹ کو اپنے موبائل فون سے کھولیں۔ </h2>

    <a class="btn" href="/">Back to home</a>
    <img src="{{ asset('assets/theme/img/not-found.svg') }} " class="img-fluid py-5" alt="Page Not Found">

  </section>

@endsection
