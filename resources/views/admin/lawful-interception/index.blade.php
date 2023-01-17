@extends('admin.layouts.app')

@section('title', 'Lawful Interception')
@section('sub-title', 'Users Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">
		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Lawful Interception</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')

		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Filter</h3>
			</div>
			<div class="panel-body">
				<form id="filter-form" class="form-inline filter-form-des" method="GET">
					<div class="row">
						<div class="col-lg-8 col-md-8 col-sm-8">
							<div class="form-group">
								<input type="text" name="search" id="search" class="form-control" placeholder="Search by Name or Email">
							</div>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2">
							<a href="{{url('admin/lawful-interception')}}">
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
				<h3 class="panel-title">Users Listing</h3>
			</div>
			<div class="panel-body">
				<table id="lawful-interception-datatable" class="table table-hover " style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Id</th>
							<th>Name</th>
							<th>Email</th>
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
	<div class="container">
		<!-- Modal -->
		<div class="modal fade" id="depictDownloadingData" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog modal-sm">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title archiving-data">Preparing Download</h4>
						<h4 class="modal-title archived-data" style="display: none">Download Data</h4>
					</div>
					<div class="modal-body">
						<p class="archiving-data">User's data is being compressed. Please wait.</p>
						<p class="archived-data" style="display: none">User's data has been compressed.
							Click the button below to download.
						</p>
						<div class="progress archiving-data">
							<div class="progress-bar progress-bar-striped bg-primary active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
							</div>
						</div>
					</div>
					<div class="modal-footer archived-data" style="display: none">
						<a class="btn btn-primary" id="download-data-btn">Download</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script>
	if ($("#lawful-interception-datatable").length) {
		$(function() {
			$('#lawful-interception-datatable').dataTable({
				stateSave: true,
				pageLength: 50,
				scrollX: true,
				processing: false,
				searching: false,
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
					url: '/admin/lawful-interception',
					data: function(d) {
						d.search = $('#search').val();
					}
				},
				columns: [{
						data: 'DT_RowIndex',
						name: 'DT_RowIndex',
						orderable: false,
						searchable: false
					},
					{
						data: 'id',
						name: 'id'
					},
					{
						data: 'name',
						name: 'name'
					},
					{
						data: 'email',
						name: 'email'
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

			$('#filter-form').on('submit', function(e) {
				e.preventDefault();
				showOverlayLoader();
				$('#lawful-interception-datatable').DataTable().draw();
			});

			$('#download-data-btn').on('click', function() {
				$('#depictDownloadingData').modal('hide');
				$('#depictDownloadingData .archiving-data').show();
				$('#depictDownloadingData .archived-data').hide();
			})

			$('body').on('click', '.lawful-download-user-data', function(e) {
				e.preventDefault();
				$('#depictDownloadingData').modal('show');

				var dataId = $(this).attr('data-id');
				var dataAction = $(this).attr('data-action');

				$.ajax({
					type: 'get',
					url: '{{ url("admin/lawful-interception/archive-user-data") }}' + '/' + dataId + '?action=' + dataAction,
					success: function(res) {
						if (res != null && res != undefined) {
							if (res.status) {
								if (res.status == 1) {
									var userAction = res.action;
									var userTempFile = setInterval(function() {
										$.ajax({
											type: 'get',
											url: '{{url("admin/lawful-interception/check-user-temp-file")}}' + '/' + dataId,
											async: false,
											success: function(res) {
												if (res != null && res != undefined) {
													if (res.status == 1) {
														clearInterval(userTempFile);
														if (userAction == 'user-files') {
															$('#download-data-btn').attr("href", '{{ url("admin/lawful-interception/user-files-download") }}' + '/' + dataId);
														} else if (userAction == 'user-all-data') {
															$('#download-data-btn').attr("href", '{{ url("admin/lawful-interception/download-all-data") }}' + '/' + dataId);
														}

														$('#depictDownloadingData .archiving-data').hide();
														$('#depictDownloadingData .archived-data').show();
													}
												}
											}
										});
									}, 5000);
								} else if (res.status == 2) // In case data is not found
								{
									$('#depictDownloadingData').modal('hide');
									window.location = res.url;
								}
							}
						} else {
							console.log('not done')
						}
					}
				});
			})
		});
	}
</script>
@endsection