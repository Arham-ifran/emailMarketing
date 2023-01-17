@extends('admin.lawful-interception.template')

@section('title', 'User Subscriptions')
@section('content')

<table style="width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px;">
    <thead style="border: solid #c7c7c7; border-width: 1px 1px 0;">
        <tr>
            <th
                style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                ID
            </th>
            <th
                style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Package
            </th>
            <th
                style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Type
            </th>
            <th
                style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Price
            </th>
            <th
                style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Start Date
            </th>
            <th
                style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                End Date
            </th>
            <th
                style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Status
            </th>
        </tr>
    </thead>
    <tbody style="border: solid #c7c7c7; border-width: 1px 1px 1px 1px;">
        @foreach ($subscriptions as $key => $subscription)
        <tr>
            <td
                style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif;">
                {{ $key+1 }}
            </td>
            <td
                style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif;">
                {{ $subscription->package_title }}
            </td>
            <td
                style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;vertical-align:top;font-family: arial, sans-serif;">
                @if ($subscription->package_id == 1)
                <span class="label label-primary">Trial</span>
                @elseif ($subscription->package_id == 2)
                <span class="label label-primary">Free</span>
                @else
                <span class="label label-success">Paid</span>
                @endif
            </td>
            <td
                style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {!! '<sup>'.config('constants.currency')['symbol'].'</sup>'.$subscription->price !!}
            </td>
            <td
                style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ \Carbon\Carbon::createFromTimeStamp($subscription->start_date, "UTC")->tz(session('timezone'))->format('d M, Y') }}
            </td>
            <td
                style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ (!empty($subscription->end_date)) ?  \Carbon\Carbon::createFromTimeStamp($subscription->end_date, "UTC")->tz(session('timezone'))->format('d M, Y') : 'Lifetime' }}
            </td>
            {{-- <td>abc</td> --}}
            <td
                style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                @if ($subscription->id == $subscription->user->package_subscription_id)
                @if (empty($subscription->end_date) || $subscription->end_date > \Carbon\Carbon::now('UTC')->timestamp)
                <span class="label label-success">Active</span>
                @else
                <span class="label label-warning">Expired</span>
                @endif
                @else
                <span class="label label-danger">In-Active</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection