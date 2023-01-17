@extends('admin.layouts.app')

@section('title', 'Languages')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Languages</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')
		<!-- DATATABLE -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Languages Listing</h3>
				@if(have_right(100))
				<div class="right">
					<a href="{{url('admin/languages/create')}}" class="pull-right">
						<button title="Add" type="button" class="btn btn-primary btn-lg btn-fullrounded">
							<span>Add</span>
						</button>
					</a>
				</div>
				@endif
			</div>
			<div class="panel-body">
				<table id="languages-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>Name</th>
							<th>Code</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($languages as $language)
						<tr>
							<td>{{$language->name}}</td>
							<td>{{$language->code}}</td>
							<td>
								@if($language->status == 0)
								<span class="label label-danger">Disable</span>
								@else
								<span class="label label-success">Active</span>
								@endif
							</td>
							<td>
								<span class="actions">
									@if(have_right(101) && $language->id != 1)
									<a class="btn btn-primary" title="Edit" href="{{url('admin/languages/' . Hashids::encode($language->id) . '/edit')}}"><i class="fa fa-pencil-square-o"></i></a>
									@endif
									<!-- @if(have_right(102))
					                  	<form method="POST" action="{{url('admin/languages/'.Hashids::encode($language->id)) }}" accept-charset="UTF-8" style="display:inline">
					                      	<input type="hidden" name="_method" value="DELETE">
					                      	<input name="_token" type="hidden" value="{{csrf_token()}}">
					                      	<button class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
					                          	<i class="fa fa-trash"></i>
					                      	</button>
					                  	</form>
										@endif -->
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
@endsection

@section('js')
<script>
	$(function() {
		$('#languages-datatable').dataTable({
			pageLength: 50,
			scrollX: true,
			responsive: true,
			// dom: 'Bfrtip',
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