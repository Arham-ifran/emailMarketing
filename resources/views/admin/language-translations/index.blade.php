@extends('admin.layouts.app')

@section('title', 'Language Translations')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Language Translations</li>
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
								<select class="form-control" name="language_module_id" id="language_module_id">
									<option value="">Select Module</option>
									@foreach ($language_modules as $language_module)
									<option value="{{$language_module->id}}">{{$language_module->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4">
							<div class="form-group">
								<select class="form-control" name="language_id" id="language_id">
									<option value="">Select Language</option>
									@foreach($languages as $lang)
									<option value="{{$lang->id}}">{{$lang->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2">
							<a href="{{url('admin/language-translations')}}">
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
				<h3 class="panel-title">Language Translations Listing</h3>
				@if(have_right(107) || have_right(104))
				<div class="right">
					@if(have_right(107))
					<a href="{{url('admin/language-translations/partial-translate')}}">
						<button type="button" title="Partial Translate" class="btn btn-primary btn-lg btn-fullrounded">
							<span>Partial Translate</span>
						</button>
					</a>
					@endif
					&nbsp;&nbsp;
					@if(have_right(104))
					<a href="{{url('admin/language-translations/create')}}">
						<button type="button" title="Bulk Translate" class="btn btn-primary btn-lg btn-fullrounded">
							<span>Bulk Translate</span>
						</button>
					</a>
					@endif
				</div>
				@endif
			</div>
			<div class="panel-body">
				<table id="language-translations-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<!-- <th>System Id</th> -->
							<!-- <th> Id</th>
							<th>Language Module</th>
							<th>Language Name</th>
							<th>Language Code</th> -->
							<!-- <th>Column Name</th> -->
							<!-- <th>Item Id</th> -->
							<!-- <th>Custom Translation</th> -->
							<th>Column</th>
							<th>Translation</th>
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
		$('#language-translations-datatable').dataTable({
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
				url: '{{url("admin/language-translations")}}',
				data: function(d) {
					d.language_module_id = $('#language_module_id option:selected').val();
					d.language_id = $('#language_id option:selected').val();
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
				//{data: 'language_module', name: 'language_module'},
				//{data: 'language_name', name: 'language_name'},
				//{data: 'language_code', name: 'language_code'},
				//{data: 'column_name', name: 'column_name'},
				// {
				// 	data: 'item_id',
				// 	name: 'item_id'
				// },
				//{data: 'custom', name: 'custom'},
				{
					data: 'column_name',
					name: 'column_name'
				},
				{
					data: 'item_value',
					name: 'item_value'
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
			$('#language-translations-datatable').DataTable().draw();
		});
	});
</script>
@endsection