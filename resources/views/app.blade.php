<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Marketing</title>

    <link rel="apple-touch-icon" sizes="32x32" href="{{ asset('images/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-96x96.png') }}">
</head>

<body class="footer-holder">
    <div id="app"></div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>