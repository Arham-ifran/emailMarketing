@extends('admin.layouts.app')
@section('title', 'Users')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Users</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')

		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Advance Filters</h3>
			</div>
			<div class="panel-body">
				<form id="users-filter-form" class="form-inline filter-form-des user-form" method="GET">
					<div class="row">
						<div class="col-lg-3 col-md-3 col-sm-12">
							<div class="form-group">
								<select class="form-control" id="status">
									<option value="">Select Status</option>
									<option value="0">Disable</option>
									<option value="1">Active</option>
									<option value="2">Unverified</option>
									<option value="3">Deleted</option>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-12">
							<a href="{{url('admin/users')}}">
								<button type="button" class="btn cancel btn-fullrounded">
									<span>Reset</span>
								</button>
							</a>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-12">
							<button type="submit" class="btn btn-primary btn-fullrounded btn-apply">
								<span>Apply</span>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- DATATABLE -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Users Listing</h3>
				@if(have_right(12))
				<div class="right">
					<a href="{{url('admin/users/create')}}" class="pull-right">
						<button title="Add" type="button" class="btn btn-primary btn-lg btn-fullrounded">
							<span>Add</span>
						</button>
					</a>
				</div>
				@endif
			</div>
			<div class="panel-body">
				<table id="users-datatable" class="table table-hover scroll-handler" style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Email</th>
							<th>Country</th>
							<th>Email Campaigns</th>
							<th>SMS Campaigns</th>
							<th>Split Campaigns</th>
							<!-- <th>Registered Platform</th> -->
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		<!-- END DATATABLE -->
	</div>
</div>
@endsection
@section('js')
<script>
	$(function() {

		$('#users-datatable').dataTable({
			pageLength: 50,
			scrollX: true,
			processing: false,
			language: {
				"processing": showOverlayLoader()
			},
			drawCallback: function() {
				hideOverlayLoader();
			},
			responsive: true,
			// dom: 'Bfrtip',
			lengthMenu: [
				[5, 10, 25, 50, 100, 200, -1],
				[5, 10, 25, 50, 100, 200, "All"]
			],
			serverSide: true,
			ajax: {
				url: '{{url("admin/users")}}',
				data: function(d) {
					d.status = $('#status').val();
				}
			},
			columns: [{
					data: 'DT_RowIndex',
					name: 'DT_RowIndex',
					orderable: false,
					searchable: false
				},
				{
					data: 'name',
					name: 'name'
				},
				{
					data: 'email',
					name: 'email'
				},
				{
					data: 'country',
					name: 'country'
				},
				{
					data: 'email_campaigns',
					name: 'email_campaigns'
				},
				{
					data: 'sms_campaigns',
					name: 'sms_campaigns'
				},
				{
					data: 'split_campaigns',
					name: 'split_campaigns'
				},
				{
					data: 'status',
					name: 'status'
				},
				{
					data: 'action',
					name: 'action',
					orderable: false,
					searchable: false
				},
			]
		}).on('length.dt', function() {
			showOverlayLoader();
		}).on('page.dt', function() {
			showOverlayLoader();
		}).on('order.dt', function() {
			showOverlayLoader();
		}).on('search.dt', function() {
			showOverlayLoader();
		});

		$('#users-filter-form').on('submit', function(e) {
			e.preventDefault();
			showOverlayLoader();
			$('#users-datatable').DataTable().draw();
		});
	});
</script>
@endsection