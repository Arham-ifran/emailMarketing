@extends('admin.lawful-interception.template')

@section('title', 'User Information')
@section('content')
<table style="width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px;">
    <thead style="border: solid #c7c7c7; border-width: 1px 1px 0;">
        <tr>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Attribute
            </th>
            <th style="text-align:left;padding:8px 10px;font-weight:bold;color:#fff;background:#009a71;font-weight:normal;font-family: arial, sans-serif;">
                Value
            </th>
        </tr>
    </thead>
    <tbody style="border: solid #c7c7c7; border-width: 1px 1px 1px 1px;">
        <tr>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif; font-weight: bold;">
                Name
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif;">
                {{ $user->name }}
            </td>
        </tr>
        <tr>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif; font-weight: bold;">
                Email
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {{ $user->email }}
            </td>
        </tr>
        <tr>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif; font-weight: bold;">
                Country
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {!! !empty($user->country_id) ? $user->country->name : '<i>(Not Set)</i>' !!}
            </td>
        </tr>
        <tr>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif; font-weight: bold;">
                Package
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                {!! !empty($user->package) ? $user->package->title : '<i>(Not Set)</i>' !!}
            </td>
        </tr>
        <tr>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif; font-weight: bold;">
                Registered Platform
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                @switch($user->platform)
                @case(1)
                Web
                @break

                @case(2)
                Mobile
                @break

                @case(3)
                Thunderbird
                @break

                @case(4)
                Outlook
                @break

                @case(5)
                Move Immunity
                @break

                @case(6)
                Ned Link
                @break

                @case(7)
                aikQ
                @break

                @case(8)
                Inbox
                @break

                @case(9)
                Overmail
                @break

                @case(10)
                Maili
                @break

                @case(11)
                Product Immunity
                @break

                @case(12)
                QR Code
                @break

                @case(13)
                TIMmunity
                @break

                @default
                Web

                @endswitch
            </td>
        </tr>
        <tr>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 1px;vertical-align:top;font-family: arial, sans-serif; font-weight: bold;">
                Status
            </td>
            <td style="font-size:14px;text-align:left;padding:8px 10px;border:solid #c7c7c7;border-width:0 1px 1px 0;font-family: arial, sans-serif;">
                @switch($user->status)
                @case(0)
                Disable
                @break

                @case(1)
                Active
                @break

                @case(2)
                Unverified
                @break

                @case(3)
                Deleted
                @break
                @endswitch
            </td>
        </tr>
    </tbody>
</table>
@endsection