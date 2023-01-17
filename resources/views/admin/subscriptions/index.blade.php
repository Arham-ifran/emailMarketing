@extends('admin.layouts.app')

@section('title', 'Users')
@section('sub-title', 'Subscriptions')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Subscriptions</li>
		</ul>
	</div>
	<div class="container-fluid">

		@include('admin.messages')
		<!-- DATATABLE -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Account Subscriptions</h3>
			</div>
			<div class="panel-body">
				<table id="subscriptions-datatable" class="table table-hover  scroll-handler" style="width:100%">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
							<th>Package</th>
							<th>Price</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Last Login</th>
							<th>Login Location</th>
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
	$(function()
    {
		$('#subscriptions-datatable').dataTable(
		{
			pageLength: 50,
			scrollX: true,
			processing: false,
			paging: true,    
			language: { "processing": showOverlayLoader()},
			drawCallback : function( ) {
		        hideOverlayLoader();
		    },
			responsive: true,
			// dom: 'Bfrtip',
			lengthMenu: [[5, 10, 25, 50, 100, 200, -1], [5, 10, 25, 50, 100, 200, "All"]],
			serverSide: true,
			ajax: "{{ route('admin.subscriptions.index') }}",
			columns: [
				{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
				{data: 'name', name: 'name'},
				{data: 'package_title', name: 'package_title'},
				{data: 'price', name: 'price'},
				{data: 'start_date', name: 'start_date'},
				{data: 'end_date', name: 'end_date'},
				{data: 'last_login', name: 'last_login'},
				{data: 'login_location', name: 'login_location'},
				{data: 'status', name: 'status'},
			],
		}).on( 'length.dt', function () {
			showOverlayLoader();
		}).on('page.dt', function () {
	        showOverlayLoader();
	    }).on( 'order.dt', function () {
		    showOverlayLoader();
		}).on( 'search.dt', function () {
    		showOverlayLoader();
		});
	});
</script>
@endsection