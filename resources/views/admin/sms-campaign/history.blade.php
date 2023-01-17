@extends('admin.layouts.app')
@section('title', 'SMS Campaign Report')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>SMS Campaign Report</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')

		@php $history = json_decode($history); $doughnutData = [$history->success, $history->fail]; @endphp

		<h3> Campaign name: {{$campaign->name}} </h3>

		<input type="hidden" class="form-control" id="module" value="0" />

		<div class="">
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-nav">
					<ul class="nav nav-tabs">
						<li role="presentation" class="active">
							<a href="#report_summary" aria-controls="report_summary" role="tab" data-toggle="tab">Report Summary</a>
						</li>
						<li role="presentation">
							<a href="#recipient_activities" aria-controls="recipient_activities" role="tab" data-toggle="tab" id="sentdata">Recipient Activities</a>
						</li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active" id="report_summary">
							<div class="row">
								<div class="col-md-7 abt-rt">
									<div class="real-time">
										<h5>
											Campaign Data
										</h5>
									</div>
									<div class="rt-data">
										<h6>
											{{count($history->sent_to)}}
										</h6>
										<p>
											Total SMS Sent
										</p>
										<span>
											{{ date('d M Y h:i:s', strtotime($history->created_at))  }}
										</span>
									</div>
									<div class="rt-progress">
										<div class="progress">
											<div class="progress-bar bg-success" role="progressbar" style="width: {{ $history->success / count($history->sent_to) * 100 }}% ;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<span>
											{{ $history->success / count($history->sent_to) * 100 }}% Delivered
										</span>
									</div>
									<div class="rt-dlvr">
										<div class="dlvr">
											<div class="delivered"></div>
											<div class="rt-desc">
												<p>
													Delivered {{ $history->success / count($history->sent_to) * 100 }}%
												</p>
												<p>
													{{$history->success}} Contacts
												</p>
											</div>
										</div>
										<div class="dlvr">
											<div class="unsent"></div>
											<div class="rt-desc">
												<p>
													Unsent {{ $history->fail / count($history->sent_to) * 100 }}%
												</p>
												<p>
													{{$history->fail}} Contacts
												</p>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-5 abt-rt">
									<div class="real-time">
										<h5>
											Campaign Reach
										</h5>
									</div>
									<div class="reach-image">
										<canvas id="doughnut" width="199" height="100" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px; color: #fff;"></canvas>
										<div class="reach-desc">
											<p>
												{{count($history->sent_to)}}
											</p>
											<p>
												Total Reach
											</p>
										</div>
									</div>
								</div>
							</div>
							<div class="row reach-graph">
								<h5>
									Subject and Sender details
								</h5>
								<div class="col-md-12 sender-details">
									<div class="row d-flex">
										<!-- <div class="col-sm-2">
											<div class="sender-image">
												<img class="img-fluid" src=/images/Image4.png>
											</div>
										</div> -->
										<div class="col-sm-9 d-flex">
											<div class="about-sender d-flex">
												<ul>
													<li>
														Sender Name:
													</li>
													@if($sender_number ?? "")
													<li>
														Reply to Number:
													</li>
													@endif
													<li>
														Created on:
													</li>
													<li>
														Sent on:
													</li>
												</ul>
											</div>
											<div class="about-sender d-flex abt-send -padd">
												<ul>
													<li>
														{{$campaign->sender_name}}
													</li>
													@if($sender_number ?? "")
													<li>
														{{$campaign->sender_number}}
													</li>
													@endif
													<li>
														{{ date('d M Y h:i:s', strtotime($campaign->created_at)) }}
													</li>
													<li>
														{{ date('d M Y h:i:s', strtotime($history->created_at)) }}
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="recipient_activities">
							<div class="panel panel-default">
								<div class="panel-heading panel-heading-nav">
									<ul class="nav nav-tabs">
										<li role="presentation" class="active">
											<a href="#sent" aria-controls="sent" role="tab" data-toggle="tab">Sent</a>
										</li>
										<li role="presentation">
											<a href="#delivered" aria-controls="delivered" role="tab" data-toggle="tab" id="delivereddata">Delivered</a>
										</li>
										<li role="presentation">
											<a href="#failed" aria-controls="failed" role="tab" data-toggle="tab" id="faileddata">Failed</a>
										</li>
									</ul>
								</div>
								<div class="panel-body">
									<div class="tab-content">
										<div role="tabpanel" class="tab-pane fade in active" id="sent">
											<!-- DATATABLE -->
											<div class="text-right">
												<a target="_blank" href="{{Request::url() . '/download?module=1'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply mb-2">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="sent-to-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Number</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</div>
											<!-- END DATATABLE -->
										</div>
										<div role="tabpanel" class="tab-pane fade" id="delivered">
											<!-- DATATABLE -->
											<div class="text-right">
												<a target="_blank" href="{{Request::url() . '/download?module=2'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply mb-2">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="delivered-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Number</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</div>
											<!-- END DATATABLE -->
										</div>
										<div role="tabpanel" class="tab-pane fade" id="failed">
											<!-- DATATABLE -->
											<div class="text-right">
												<a target="_blank" href="{{Request::url() . '/download?module=3'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply mb-2">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="failed-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Number</th>
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
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
@endsection
@section('js')
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js'></script>
<script>
	$(document).ready(function() {
		var ctx = $("#doughnut");
		var myLineChart = new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: ["Delivered", "Failed"],
				datasets: [{
					data: @json($doughnutData),
					backgroundColor: ["#24e096", "#fd6a21", "#ffbb28", ],
					borderColor: "transparent",
				}]
			},
			options: {
				legend: {
					labels: {
						fontColor: "#fff",
						fontSize: 14
					}
				},
				// title: {
				// 	display: false,
				// 	text: 'Reach',
				// 	fontColor: "#fff",
				// 	fontSize: 18
				// }
			}
		});
	});
</script>
<script>
	var sent = 0;
	var delivered = 0;
	var failed = 0;

	$('#sentdata').on('click', function() {
		if (!sent) {
			$('#sent-to-datatable').dataTable({
				searching: true,
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
					url: window.location,
					data: function(d) {
						d.module = 1;
					}
				},
				columns: [{
						data: 'DT_RowIndex',
						name: 'DT_RowIndex',
						orderable: false,
						searchable: false
					},
					{
						data: 'first_name',
						name: 'first_name'
					},
					{
						data: 'last_name',
						name: 'last_name'
					},
					{
						data: 'email',
						name: 'email'
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
			sent = 1;
		}
	})

	$('#delivereddata').on('click', function() {
		if (!delivered) {
			$('#delivered-datatable').dataTable({
				searching: true,
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
					url: window.location,
					data: function(d) {
						d.module = 2;
					}
				},
				columns: [{
						data: 'DT_RowIndex',
						name: 'DT_RowIndex',
						orderable: false,
						searchable: false
					},
					{
						data: 'first_name',
						name: 'first_name'
					},
					{
						data: 'last_name',
						name: 'last_name'
					},
					{
						data: 'email',
						name: 'email'
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
			delivered = 1;
		}
	})

	$('#faileddata').on('click', function() {
		if (!failed) {
			$('#failed-datatable').dataTable({
				searching: true,
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
					url: window.location,
					data: function(d) {
						d.module = 3;
					}
				},
				columns: [{
						data: 'DT_RowIndex',
						name: 'DT_RowIndex',
						orderable: false,
						searchable: false
					},
					{
						data: 'first_name',
						name: 'first_name'
					},
					{
						data: 'last_name',
						name: 'last_name'
					},
					{
						data: 'email',
						name: 'email'
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
			failed = 1;
		}
	})
</script>
@endsection