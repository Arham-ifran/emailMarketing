@extends('admin.layouts.app')

@section('title', 'Language Modules')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Language Modules</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')

		<!-- DATATABLE -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Language Modules Listing</h3>
			</div>
			<div class="panel-body">
				<table id="language-modules-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Columns</th>
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
		$('#language-modules-datatable').dataTable(
		{
			stateSave: true,
			pageLength: 50,
			scrollX: true,
			processing: false,
			language: { "processing": showOverlayLoader()},
			drawCallback : function( ) {
		        hideOverlayLoader();
		    },
			responsive: true,
			// dom: 'Bfrtip',
			lengthMenu: [[5, 10, 25, 50, 100, 200, -1], [5, 10, 25, 50, 100, 200, "All"]],
			serverSide: true,
			ajax: {
                url: '{{url("admin/language-modules")}}',
            },
			columns: [
				{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
				{data: 'name', name: 'name'},
				{data: 'columns', name: 'columns'},
			]
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