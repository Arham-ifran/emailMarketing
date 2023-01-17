<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{settingValue('site_title')}}</title>
</head>

<body style="padding:0; margin:0px; background:#eee;">

    <div style="max-width:600px; margin:auto; text-align:center;  background:#fff; color:#222; font-size:17px; position: relative;">
        <div style="text-align: left;">
            {!! html_entity_decode($content) !!}
        </div>

        <div style=" text-align: center; background: #24e096 ;padding:10px 20px 10px;  ">
            @if($reply_to)
            <p style="font-size:12px; color: #fffcfc; margin-bottom: 0; font-family: sans-serif;"> In regards to this email, Reply to: <u> {{$reply_to}} </u> </p>
            @endif
            <p style="font-size:12px; color: #fffcfc; margin-bottom: 0; font-family: sans-serif;"> Don't want to receive any more campaigns from this sender? <a href="{{$unsubscribe}}"> <u> Unsubscribe </u> </a> </p>
        </div>
        <!--footer area-->
        <div style=" background: #1D2579; padding:10px 20px 10px; font-size: 12px; font-family: sans-serif; color: #fff;  align-items: center; justify-content: center;  text-align: center;">
            <!-- copyright area-->
            <p style="margin-bottom: 0; margin-top: 0; display: block; margin:0 auto; text-align: center;">This campaign was designed and created on
            </p>
            <a href="{{ url('/') }}" style="display: block; margin:5px auto;text-align: center;">
                <img style="width: 120px;" src="{{asset('/images/admin-logo.png')}}" alt="EMarketingLogo">
            </a>
        </div>
    </div>
</body>

</html>