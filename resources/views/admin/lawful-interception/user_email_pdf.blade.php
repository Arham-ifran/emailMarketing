@extends('admin.lawful-interception.template')

@section('title', 'User SMS Campaigns')
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
        @foreach ($emailcampaigns as $key => $campaign)
        @php
        if ($campaign->status == 1) {
        $status = 'Active';
        } elseif ($campaign->status == 2) {
        $status = 'Draft';
        } elseif ($campaign->status == 3) {
        $status = 'Disabled';
        } elseif ($campaign->status == 4) {
        $status = 'Sending';
        } elseif ($campaign->status == 5) {
        $status = 'Sent';
        } elseif ($campaign->status == 6) {
        $status = 'Stopped';
        } elseif ($campaign->status == 7) {
        $status = 'Processing';
        }

        if($campaign->campaign_type == 1) $type = 'Immidiate';
        else if($campaign->campaign_type == 2) $type = 'Schedule';
        else if($campaign->campaign_type == 3) $type = 'Recursive';

        $processed = $campaign->reports->count() ? (sizeof($campaign->reports()->get()[sizeof($campaign->reports()->get()) - 1]['sent_to']) ? ($campaign->reports()->get()[$campaign->reports()->count() - 1]['sent_to'][sizeof($campaign->reports()->get()[$campaign->reports()->count() - 1]['sent_to']) - 1]['pivot']['started_at']) : $campaign->reports()->get()[$campaign->reports->count() - 1]->created_at) : $campaign->updated_at;
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