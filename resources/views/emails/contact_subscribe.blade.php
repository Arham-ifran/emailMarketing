<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{settingValue('site_title')}}</title>
</head>

<body style="padding:0; margin:0px; background:#eee;">

    <div style="max-width:600px; margin:auto;  background:#fff; color:#222; font-size:17px; position: relative;">
        <div style="width:100%; padding:15px 0px 35px; text-align:center; margin-bottom: 15px; background-size: cover;">
            <img src="{{ isset($asset_url) ? $asset_url.'img/logo-blue-i.png' : asset('img/logo-blue-i.png') }}">
            <br>
            <img src="{{ isset($asset_url) ? $asset_url.'images/logo-text.png' : asset('images/logo-text.png') }}" width="180">
        </div>

        {!! $content !!}

        <div style="margin-top: 40px; text-align: center; background: green ;padding:10px 20px 10px;  ">
            <p style="font-size:12px; color: #fffcfc; margin-bottom: 0; font-family: sans-serif;"> Subscribe </p>
            <a style="color: #fffcfc; font-size:12px;font-family: sans-serif; " href="{!! $subscription_link !!}">click here</a>
        </div>
        <div style="margin-top: 40px; text-align: center; background: #f55d04;padding:10px 20px 10px;  ">
            <p style="font-size:12px; color: #fffcfc; margin-bottom: 0; font-family: sans-serif;"> unSubscribe </p>
            <a style="color: #fffcfc; font-size:12px;font-family: sans-serif; " href="{!! $unsubscription_link !!}">click here</a>
        </div>
        <!--footer area-->
        <div style=" background: #1c639e; padding:10px 20px 10px; font-size: 12px;text-align: center; font-family: sans-serif;  color: #fff;">
            <!-- copyright area-->
            <p style="margin-bottom: 0; margin-top: 0">&copy; {{date('Y')}} {{settingValue('site_title')}}.
                All rights reserved
            </p>
        </div>
    </div>
</body>

</html>