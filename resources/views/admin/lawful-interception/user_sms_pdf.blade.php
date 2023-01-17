@extends('admin.lawful-interception.template')

@section('title', 'User Email Campaigns')
@section('content')

<table style="width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px;">
    <thead style="border: solid #c7c7c7; border-width: 1px 1px 0;">
        <tr>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                ID
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Campaign Name
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Sending Type
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Creation Date
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Last Processing Date
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Status
            </th>
        </tr>
    </thead>
    <tbody style="border: solid #c7c7c7; border-width: 1px 1px 1px 1px;">
        @foreach ($smscampaigns as $key => $campaign)
        @php
        if ($campaign->status == 1) $status = 'Draft';
        else if ($campaign->status == 2) $status = 'Sending';
        else if ($campaign->status == 3) $status = 'Sent';
        else if ($campaign->status == 4) $status = 'Disabled';
        else if ($campaign->status == 5) $status = 'Active';
        else if ($campaign->status == 6) $status = 'Stopped';
        else if ($campaign->status == 7) $status = 'Processing';

        if($campaign->type == 1) $type = 'Immidiate';
        else if($campaign->type == 2) $type = 'Schedule';
        else if($campaign->type == 3) $type = 'Recursive';

        $processed = $campaign->reports->count() ? ($campaign->reports()->get()[0]['sent_to']->count() ? $campaign->reports()->get()[0]['sent_to'][0]['pivot']['started_at'] : $campaign->reports[0]->created_at) : $campaign->updated_at;
        @endphp
        <tr>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif;">
                {{ $key+1 }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif;">
                {{ $campaign->name }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ $type }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ \Carbon\Carbon::parse($campaign->created_at)->format('d M Y') }}
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                @if($processed == 'Pending')
                Pending
                @else
                {{ \Carbon\Carbon::parse($processed)->format('d M Y') }}
                @endif
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                <span class="label label-success">{{ $status }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection