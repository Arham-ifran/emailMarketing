@extends('admin.lawful-interception.template')

@section('title', 'User Payments')
@section('content')

<table style="width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px;">
    <thead style="border: solid #c7c7c7; border-width: 1px 1px 0;">
        <tr>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                ID
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Package
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Amount
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                VAT %
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                VAT Amount
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Discount %
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Discount Amount
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Paid Amount
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Reseller
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Voucher
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Payment Source
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Payment Date
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Status
            </th>
        </tr>
    </thead>
    <tbody style="border: solid #c7c7c7; border-width: 1px 1px 1px 1px;">
        @foreach ($payments as $key => $payment)
        <tr>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;font-family: arial, sans-serif;">
                {{ $key+1 }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ $payment->item }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {!! '<sup>'.config('constants.currency')['symbol'].'</sup>'.$payment->amount !!}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ $payment->vat_percentage }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {!! '<sup>'.config('constants.currency')['symbol'].'</sup>'.$payment->vat_amount !!}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ $payment->discount_percentage }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {!! '<sup>'.config('constants.currency')['symbol'].'</sup>'.$payment->discount_amount !!}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {!! '<sup>'.config('constants.currency')['symbol'].'</sup>'.$payment->total_amount !!}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ $payment->voucher ? $payment->reseller : 'N/A' }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ $payment->voucher ? $payment->voucher : 'N/A' }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{$payment->payment_method}}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ \Carbon\Carbon::createFromTimeStamp($payment->timestamp, "UTC")->tz(session('timezone'))->format('d M, Y - h:i A') }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ $payment->status == 1 ? 'Paid' : 'In-process'}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection