@extends('admin.layouts.app')
@section('title', 'Email Campaigns')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Email Campaigns</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')

		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Advance Filters</h3>
			</div>
			<div class="panel-body">
				<form id="campaign-filter-form" class="form-inline filter-form-des campaign-form" method="GET">
					<div class="row">
						<div class="col-lg-3 col-md-3 col-sm-12">
							<div class="form-group">
								<input type="text" class="form-control" id="search" placeholder="search for campaign or user name" />
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-12">
							<div class="form-group">
								<select class="form-control" id="status">
									<option value="">Select Status</option>
									<option value="1">Active</option>
									<option value="2">Draft</option>
									<option value="3">Disabled</option>
									<option value="4">Sending</option>
									<option value="5">Sent</option>
									<option value="6">Stopped</option>
									<option value="7">Processing</option>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-12">
							<a href="{{Request::fullUrl()}}">
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
				<h3 class="panel-title">Email Campaigns Listing</h3>
			</div>
			<div class="panel-body">
				<table id="campaigns-datatable" class="table table-hover scroll-handler" style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<th>User Email</th>
							<th>Campaign Name</th>
							<th>Link Clicks</th>
							<th>Open Rate</th>
							<th>Sending Type</th>
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
	function checkname() {
		let params = new URLSearchParams(location.search);
		if (params.get('user')) {
			return params.get('user');
		}
		return '';
	}
	const userid = checkname();

	$(function() {
		$('#campaigns-datatable').dataTable({
			searching: false,
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
				url: '{{url("admin/email-campaigns")}}',
				data: function(d) {
					d.status = $('#status').val();
					d.search = $('#search').val();
					d.userid = userid;
				}
			},
			columns: [{
					data: 'DT_RowIndex',
					name: 'DT_RowIndex',
					orderable: false,
					searchable: false
				},
				{
					data: 'user_email',
					name: 'user_email'
				},
				{
					data: 'name',
					name: 'name'
				},
				{
					data: 'track_clicks',
					name: 'track_clicks',
				},
				{
					data: 'track_opens',
					name: 'track_opens',
				},
				{
					data: 'sending_type',
					name: 'sending_type'
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

		$('#campaign-filter-form').on('submit', function(e) {
			e.preventDefault();
			showOverlayLoader();
			$('#campaigns-datatable').DataTable().draw();
		});
	});
</script>
@endsection