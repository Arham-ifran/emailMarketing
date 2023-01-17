<html lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sending Report</title>

    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->

</head>

<body style="width: 100%;margin: 0 auto;color: #0010289c;background: #FFFFFF;font-size: 12px;">
    <div style="text-align: center; font-size:20px;">
        <a href="/">
            <!-- <img src="{{ asset('images/admin-logo.png') }}" alt="logo" class="img-responsive logo"> -->
            <img style="margin-bottom: 20px;" src="{{ public_path('images/admin-login-logo.png') }}"><br>
        </a>
        <h5 style="border-top: 1px solid #5D6975;border-bottom: 1px solid #5D6975; color: #5D6975;font-size: 30px;line-height: 1.4em;font-weight: normal;text-align: center;margin: 10px 0 20px 0;text-transform:capitalize;"> Campaign name: <span style="font-size: 26px;"> {{ $name }}</span>
        </h5>
    </div>
    <div style="text-align: left; font-size:16px; display:block; width: 100%; margin-bottom: 15px;">
        <div style="width: 50%; float:left;"><b>Initiated at:</b> {{$initiated_at}}</div>
        <div style="width: 50%; float:right;"><b>Processed at:</b> {{$processed_at}} </div>
    </div>

    <div class="row">
        @foreach ($data as $report)
        <table id="example1" style="width: 100%;border-collapse: collapse;border-spacing: 0;margin-bottom: 20px;">
            <thead style="border:solid #c7c7c7;border-width:1px 1px 0;">

                <tr class="text-center">
                    <!-- <th class="text-center">Report Id</th> -->
                    <!-- <th class="text-center" style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#007dbd;font-weight:normal;">Contacts selected</th>
                    <th class="text-center" style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#007dbd;font-weight:normal;">Sent to</th>
                    <th class="text-center" style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#007dbd;font-weight:normal;">Sending fails</th>
                    <th class="text-center" style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#007dbd;font-weight:normal;">Sent on</th> -->
                    <th width="50%" style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#1D2579;font-weight:normal;font-size:20px;">Attribute</th>
                    <th width="50%" style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#1D2579;font-weight:normal;font-size:20px;">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top; font-weight:bold;">Contacts selected</td>
                    <td width="15%" class="text-center" style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;">{{ $report->success->count() + $report->fail->count() }}</td>
                </tr>
                <tr>
                    <td class="text-center" style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-weight:bold;">Sent to</td>
                    <td width="15%" class="text-center" style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;">{{ $report->success->count() }}</td>
                </tr>
                <tr>
                    <td class="text-center" style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-weight:bold;">Sending fails</td>
                    <td width="15%" class="text-center" style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;">{{ $report->fail->count() }}</td>
                </tr>

            </tbody>

        </table>
        @endforeach
    </div>


    <div class="row">
        <table border="0" width="100%">
            <tbody>
                <tr>
                    <td style="width: 40%"></td>
                    <td style="width: 20%"></td>
                    <td style="width: 40%; text-align:center;">
                        <!-- <p style="text-align: center;">Owner Signature</p> -->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>