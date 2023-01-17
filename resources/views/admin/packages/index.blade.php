@extends('admin.layouts.app')

@section('title', 'Packages')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Packages</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')
		<!-- DATATABLE -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Packages Listing</h3>
				@if(have_right(16))
				<div class="right">
					<a href="{{url('admin/packages/create')}}" class="pull-right">
						<button type="button" title="Add" class="btn btn-primary btn-lg btn-fullrounded">
							<span>Add</span>
						</button>
					</a>
				</div>
				@endif
			</div>
			<div class="panel-body">
				<table id="packages-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<!-- <th>Id</th> -->
							<th>Title</th>
							<th>Sub-Title</th>
							<th>Type</th>
							<th>Monthly Price</th>
							<th>Yearly Price</th>
							<th>Total Users</th>
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
		$('#packages-datatable').dataTable({
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
			ajax: "{{url('admin/packages')}}",
			columns: [{
					data: 'DT_RowIndex',
					name: 'DT_RowIndex',
					orderable: false,
					searchable: false
				},
				// {data: 'id', name: 'id'},
				{
					data: 'title',
					name: 'title'
				},
				{
					data: 'sub_title',
					name: 'sub_title'
				},
				{
					data: 'type',
					name: 'type'
				},
				{
					data: 'monthly_price',
					name: 'monthly_price'
				},
				{
					data: 'yearly_price',
					name: 'yearly_price'
				},
				{
					data: 'total_users',
					name: 'total_users'
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
	});
</script>
@endsection