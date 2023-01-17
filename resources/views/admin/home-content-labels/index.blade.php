@extends('admin.layouts.app')

@section('title', 'Home Content Labels')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Home Content Labels</li>
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
								<select class="form-control" name="home_content_id" id="home_content_id">
									<option value="">Select Home Content</option>
									@foreach($home_contents as $home_content)
									<option value="{{ $home_content->id }}">
										{{ $home_content->name }}
									</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4">

						</div>
						<div class="col-lg-2 col-md-2 col-sm-2">
							<a href="{{url('admin/home-content-labels')}}">
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
				<h3 class="panel-title">Home Content Labels Listing</h3>
				@if(have_right(75))
				<div class="right">
					<a href="{{url('admin/home-content-labels/create')}}" class="pull-right">
						<button type="button" title="Add" class="btn btn-primary btn-lg btn-fullrounded">
							<span>Add</span>
						</button>
					</a>
				</div>
				@endif
			</div>
			<div class="panel-body">
				<table id="home-content-labels-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<!-- <th>System Id</th> -->
							<th>Home Content</th>
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
		$('#home-content-labels-datatable').dataTable({
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
				url: '{{url("admin/home-content-labels")}}',
				data: function(d) {
					d.home_content_id = $('#home_content_id option:selected').val();
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
					data: 'home_content',
					name: 'home_content'
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
			$('#home-content-labels-datatable').DataTable().draw();
		});
	});
</script>
@endsection