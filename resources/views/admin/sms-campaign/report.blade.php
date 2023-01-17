@extends('admin.layouts.app')

@section('title', 'Campaigns')
@section('sub-title', $action.' Campaign')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/sms-campaigns')}}"><i class="fa fa-comments"></i>sms Campaigns</a></li>
			@if($action == 'History') <li> <a href="{{$prev_url}}"><i class="fa fa-list"></i>View Campaign</a> </li> @endif
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">

		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">{{$campaign->name}} Report @if($action == 'History') History @endif</h3>
			</div>
			<div class="panel-body">
				<table class="table table-hover" style="width:100%">
					@if($action == 'View')
					<thead>
						<tr>
							<th>#</th>
							<th>Contacts selected</th>
							<th>Successfully Sent</th>
							<th>Sending fails</th>
							<th>Date Sent</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($reports as $report)
						<tr>
							<td> {{$loop->index+1}} </td>
							<td> {{$report['success'] + $report['fail']}} </td>
							<td> {{$report['success']}} </td>
							<td> {{$report['fail']}} </td>
							<td> {{$report['created_at']}} </td>
							<td> <a class="" href="{{\Request::getRequestUri()}}/{{$report['hash_id']}}" title="Contact Details"><i class="fa fa-list-alt"></i></a> </td>
						</tr>
						@endforeach
					</tbody>

					@elseif($action == 'History' && count($reports[$index]['sent_to']))
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Number</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($reports[$index]['sent_to'] as $contact)
						<tr>
							<td> {{$loop->index+1}} </td>
							<td> {{$contact['first_name'] . " " . $contact['last_name']}} </td>
							<td> {{$contact['number']}} </td>
							<td> <span class="label label-{{$contact['pivot']['sent_at']?'success':'danger'}}"> {{$contact['pivot']['sent_at'] ? "Sent" : "Failed"}} </span> </td>
						</tr>
						@endforeach
					</tbody>
					@endif
				</table>
			</div>
		</div>
	</div>
</div>
@endsection