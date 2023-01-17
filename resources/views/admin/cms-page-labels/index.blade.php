@extends('admin.layouts.app')

@section('title', 'CMS Page Labels')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>CMS Page Labels</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')

		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Advance Filters</h3>
			</div>
			<div class="panel-body">
				<form id="filter-form" class="form-inline filter-form-des" method="GET">
					<div class="row">
						<div class="col-lg-4 col-md-4 col-sm-4">
							<div class="form-group">
								<select class="form-control" name="cms_page_id" id="cms_page_id">
									<option value="">Select CMS Page</option>
									@foreach($cms_pages as $cms_page)
									<option value="{{ $cms_page->id }}">
										{{ $cms_page->title }}
									</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4">

						</div>
						<div class="col-lg-2 col-md-2 col-sm-2">
							<a href="{{url('admin/cms-page-labels')}}">
								<button type="button" class="btn cancel btn-fullrounded">
									<span>Reset</span>
								</button>
							</a>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2">
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
				<h3 class="panel-title">CMS Page Labels Listing</h3>
				@if(have_right(123))
				<div class="right">
					<a href="{{url('admin/cms-page-labels/create')}}" class="pull-right">
						<button type="button" title="Add" class="btn btn-primary btn-lg btn-fullrounded">
							<span>Add</span>
						</button>
					</a>
				</div>
				@endif
			</div>
			<div class="panel-body">
				<table id="cms-page-labels-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<!-- <th>System Id</th> -->
							<th>CMS Page</th>
							<th>Label</th>
							<th>Value</th>
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
		$('#cms-page-labels-datatable').dataTable({
			stateSave: true,
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
				url: '{{url("admin/cms-page-labels")}}',
				data: function(d) {
					d.cms_page_id = $('#cms_page_id option:selected').val();
				}
			},
			columns: [{
					data: 'DT_RowIndex',
					name: 'DT_RowIndex',
					orderable: false,
					searchable: false
				},
				// {
				// 	data: 'id',
				// 	name: 'id'
				// },
				{
					data: 'cms_page',
					name: 'cms_page'
				},
				{
					data: 'label',
					name: 'label'
				},
				{
					data: 'value',
					name: 'value'
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

		$('#filter-form').on('submit', function(e) {
			e.preventDefault();
			$('#cms-page-labels-datatable').DataTable().draw();
		});
	});
</script>
@endsection