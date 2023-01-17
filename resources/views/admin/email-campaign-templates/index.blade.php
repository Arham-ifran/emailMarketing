@extends('admin.layouts.app')

@section('title', 'Email Campaign Templates')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Email Campaign Templates</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')
		<!-- DATATABLE -->
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Email Campaign Templates Listing</h3>
				@if(have_right(83))
				<div class="right">
					<a href="{{url('admin/email-campaign-templates/create')}}" class="pull-right">
						<button type="button" class="btn btn-primary btn-lg btn-fullrounded">
							<span>Add</span>
						</button>
					</a>
				</div>
				@endif
			</div>
			<div class="panel-body">
				<table id="email-templates-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($email_templates as $key => $email_template)
						<tr>
							<td>{{ $key+1 }}</td>
							<td>{{$email_template->name}}</td>
							<td>
								@php
								if ($email_template->status == 1){
									echo '<span class="label label-success">Active</span>';
								}
								else{
									echo '<span class="label label-danger">Disable</span>';
								}
								@endphp
							</td>
							<td>
								<span class="actions">
									@if(have_right(84))
									<a class="btn btn-primary" href="{{url('admin/email-campaign-templates/' . Hashids::encode($email_template->id) . '/edit')}}" title="Edit"><i class="fa fa-pencil-square-o"></i></a>
									@endif
									@if(have_right(85))
									<form id="delete_{{Hashids::encode($email_template->id)}}" method="POST" action="{{url('admin/email-campaign-templates/'.Hashids::encode($email_template->id)) }}" accept-charset="UTF-8" style="display:inline">
										<input type="hidden" name="_method" value="DELETE">
										<input name="_token" type="hidden" value="{{csrf_token()}}">
										<button class="btn btn-danger" type="button" onclick="openDeletePopup('delete_{{Hashids::encode($email_template->id)}}')">
											<i class="fa fa-trash"></i>
										</button>
									</form>
									@endif
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
		$('#email-templates-datatable').dataTable({
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