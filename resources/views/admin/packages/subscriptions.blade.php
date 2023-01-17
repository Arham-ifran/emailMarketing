@extends('admin.layouts.app')

@section('title', 'Packages')
@section('sub-title', 'Subscriptions')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/packages')}}"><i class="fa fa-list"></i> Packages</a></li>
			<li>Subscriptions</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')
		<!-- DATATABLE -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Subscriptions</h3>
				@if (Hashids::decode($id) && Hashids::decode($id)[0])
				<div class="right">
					<span class="label label-default" style="font-size: 90%;">{{getValue('packages','title',array('id' => Hashids::decode($id)[0]))}}
						Package</span>
				</div>
				@endif
			</div>
			<div class="panel-body">
				<table id="subscriptions-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Price</th>
							<th>Start Date</th>
							<th>End Date</th>
							<!-- <th>Last Login</th> -->
							<!-- <th>Login Location</th> -->
							<th>Status</th>
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
		$('#subscriptions-datatable').dataTable({
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
			// ajax: "{{url('admin/packages/subscriptions/'.$id)}}",
			ajax: {
				url: '{{url("admin/packages/subscriptions/".$id)}}',
				data: function(d) {
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
					data: 'name',
					name: 'name'
				},
				{
					data: 'price',
					name: 'price'
				},
				{
					data: 'start_date',
					name: 'start_date'
				},
				{
					data: 'end_date',
					name: 'end_date'
				},
				// {
				// 	data: 'last_active_at',
				// 	name: 'last_active_at'
				// },
				// {
				// 	data: 'login_location',
				// 	name: 'login_location'
				// },
				{
					data: 'status',
					name: 'status'
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
	});
</script>
@endsection