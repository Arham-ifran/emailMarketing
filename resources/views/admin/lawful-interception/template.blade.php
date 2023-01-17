<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
    </script>
</head>

<body style="width: 100%;margin: 0 auto;color: #0010289c;background: #FFFFFF;font-size: 12px;">
    <header class="clearfix" style="padding: 10px 0;margin-bottom:10px;font-family: arial, sans-serif;">
        <div style="width:100%; padding:15px 0px 35px; text-align:center; margin-bottom: 15px;background-size: cover;">
            {{-- <img src="{{ public_path('images/logo-blue-i.png') }}"><br>
            <img src="{{ public_path('images/logo-text.png') }}" width="180"> --}}
            <!-- <img src="{{ public_path('images/logo-green.png') }}"><br> -->
            <img src="{{ public_path('images/admin-login-logo.png') }}" width="180">

        </div>
        <div style="width:60%;float:left;">
            <h4 style="margin:0 0 10px;color:#333;text-decoration: underline;font-weight: bold"><b>@yield('title')</b>
            </h4>
            <br>
            <span style="color:#333;display: inline-block;vertical-align:top;margin-right:5px;font-size:14px;font-weight:500;">
                Date : {{ \Carbon\Carbon::now()->format('d M, Y') }}
            </span>
        </div>
        <div class="clearfix"></div>
    </header>
    <main>
        @yield('content')
    </main>

    <footer style="color:#333;width: 90%;position: absolute;bottom: 0;border-top: 1px solid #009a71;padding: 8px 10px;text-align: center;left:0; right:0; margin: 0 auto;font-family: arial, sans-serif;">
        This pdf is generated by {{ settingValue('site_title') }}
    </footer>
</body>

</html>