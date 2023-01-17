<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{settingValue('site_title')}}</title>
</head>

<body style="padding:0; margin:0px; background:#eee;">

    <div style="max-width:600px; margin:auto;  background:#fff; color:#222; font-size:17px; position: relative; word-break: break-word;">
        <div style="width:100%; padding:15px 0px 35px; text-align:center; margin-bottom: 15px; background-size: cover;">
            <a href="/">
                <img class="img-fluid" src="{{public_path('/images/admin-login-logo.png')}}" alt="EMarketingLogo">
            </a>
        </div>
        <h3>
            Invoice
        </h3>
        <p>Placeholder invoice file.</p>

        <div style="margin-top: 40px; text-align: center; background: #24e096 ;padding:10px 20px 10px;  ">
            <a style=" word-break: break-all; color: #fffcfc; font-size:12px;font-family: sans-serif; " href={{url('/')}}>{{ url('/') }}</a>
        </div>
        <!--footer area-->
        <div style=" background: #1D2579; padding:10px 20px 10px; font-size: 12px;text-align: center; font-family: sans-serif;  color: #fff;">
            <!-- copyright area-->
            <p style="margin-bottom: 0; margin-top: 0">&copy; {{date('Y')}} {{settingValue('site_title')}}.
                All rights reserved
            </p>
        </div>
    </div>
</body>

</html>