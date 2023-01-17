@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('sub-title', 'Overview & Statistics')

@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

	</div>
	<div class="container-fluid">
		@include('admin.messages')
		<div class="row">
			<div class="col-md-12">
				<!-- OVERVIEW -->
				<div class="panel dark-theme">
					<div class="panel-body dashboard-cards">
						<div class="row margin-bottom-30">
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-primary box-primary-shadow">
									<div class="right">
										<span class="value">{{ $users }}</span>
										<span class="title">Users</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-users"></i></span>
								</div>
							</div>
							<!-- <div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate">
									<span class="icon-wrapper custom-bg-blue"><i class="fa fa-question"></i></span>
									<div class="right">
										<span class="value">{{ $faqs }}</span>
										<span class="title">FAQs</span>
									</div>
								</div>
							</div> -->
							<!-- <div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-info box-info-shadow">
									<div class="right">
										<span class="value">{{ $cms_pages }}</span>
										<span class="title">CMS Pages</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-file-text-o"></i></span>
								</div>
							</div> -->
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-green">
									<!-- fa fa-th-large -->
									<div class="right">
										<span class="value">{{ $roles }}</span>
										<span class="title">Roles</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-user-secret"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-yellow">
									<div class="right">
										<span class="value">{{ $admins }}</span>
										<span class="title">Admin Users</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-user"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-secondary box-secondary-shadow">
									<div class="right">
										<span class="value">{{ number_format($received_payment, 2, '.', '')  }}</span>
										<span class="title">Received Payment
											({{config('constants.currency')['symbol']}})</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-credit-card"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-info box-info-shadow">
									<div class="right">
										<span class="value">{{ $packages }}</span>
										<span class="title">Packages</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-list"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-purple box-info-shadow">
									<div class="right">
										<span class="value">{{ $faqs }}</span>
										<span class="title">FAQs</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-question"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-dark-green box-info-shadow">
									<div class="right">
										<span class="value">{{ $cms_pages }}</span>
										<span class="title">CMS Pages</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-file-text-o"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-success box-success-shadow">
									<div class="right">
										<span class="value">{{ $email_templates }}</span>
										<span class="title">Email Templates</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-envelope"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-red">
									<div class="right">
										<span class="value">{{ $email_campaigns }}</span>
										<span class="title">Email Campaigns</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-envelope"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-orange">
									<div class="right">
										<span class="value">{{ $sms_campaigns }}</span>
										<span class="title">SMS Campaigns</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-comments"></i></span>
								</div>
							</div>
							<div class="col-md-3 col-xs-6">
								<div class="widget-metric_6 animate bg-pink box-info-shadow">
									<div class="right">
										<span class="value">{{ $split_campaigns }}</span>
										<span class="title">Split Campaigns</span>
									</div>
									<span class="icon-wrapper"><i class="fa fa-envelope"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- END OVERVIEW -->
			</div>
		</div>

		@if(have_right(11))
		@if(count($deleted_users) > 0)
		@endif
		<div class="row">

			<div class="col-md-12">
				<div class="alert alert-danger persist-alert" role="alert">
					<center>
						Following users will be deleted on specific deletion date/time
					</center>
				</div>
			</div>

			<div class="col-md-12">
				<!-- DATATABLE -->
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">Users Listing</h3>
					</div>
					<div class="panel-body">
						<table id="recent-users" class="table table-hover " style="width:100%">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Email</th>
									<th>Deletion Date/Time</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($deleted_users as $user)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>{{ $user->name }}</td>
									<td>{{$user->email}}</td>
									<td>{{\Carbon\Carbon::createFromTimeStamp(strtotime($user->deleted_at . "+" . settingValue('user_deletion_days'). " days"), "UTC")->tz(session('timezone'))->format('d M, Y h:i:s a')}}
									</td>
									<td>
										<span class="label label-danger">Deleted</span>
									</td>
									<td>
										<span class="actions">
											@if(have_right(13))
											<a class="btn btn-primary" title="Edit" target="_blank" href="{{url('admin/users/' . Hashids::encode($user->id) . '/edit')}}"><i class="fa fa-pencil-square-o"></i></a>
											@endif
											@if(have_right(14))
											<form id="delete_{{Hashids::encode($user->id)}}" method="POST" action="{{url('admin/users/'.Hashids::encode($user->id)) }}" accept-charset="UTF-8" style="display:inline">
												<input type="hidden" name="_method" value="DELETE">
												<input type="hidden" name="page" value="dashboard">
												<input name="_token" type="hidden" value="{{csrf_token()}}">
												<button class="btn btn-danger" title="Delete" type="button" onclick="openDeletePopup('delete_{{Hashids::encode($user->id)}}')">
													<i class="fa fa-trash"></i>
												</button>
											</form>
											@endif
										</span>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<!-- END DATATABLE -->
			</div>
		</div>
		@endif
	</div>
</div>
@endsection

@section('js')
<script>
	if ($("#recent-users").length)
		$(function() {
			$('#recent-users').dataTable({
				pageLength: 50,
				scrollX: true,
				responsive: true,
				//dom: 'Bfrtip',
				lengthMenu: [
					[5, 10, 25, 50, 100, 200, -1],
					[5, 10, 25, 50, 100, 200, "All"]
				],
				language: {
					"processing": showOverlayLoader()
				},
				drawCallback: function() {
					hideOverlayLoader();
				},
			}).on('length.dt', function() {
				showOverlayLoader();
			}).on('page.dt', function() {
				showOverlayLoader();
			}).on('order.dt', function() {
				showOverlayLoader();
			}).on('search.dt', function() {
				showOverlayLoader();
			});
		});
</script>
@endsection