@extends('admin.layouts.app')
@section('title', 'Split Campaign Report')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li>Split Campaigns Report</li>
		</ul>
	</div>
	<div class="container-fluid">
		@include('admin.messages')


		@php $history = json_decode($history); $doughnutData = [$history->success, $history->bounces, $history->fail]; @endphp

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
						<li role="presentation">
							<a href="#click_activities" aria-controls="click_activities" role="tab" data-toggle="tab" id="clickdata">Click Activities</a>
						</li>
						<li role="presentation">
							<a href="#bounces" aria-controls="bounces" role="tab" data-toggle="tab" id="bouncedata">Bounces</a>
						</li>
						<li role="presentation">
							<a href="#split" aria-controls="split" role="tab" data-toggle="tab" id="splitdata">Split report</a>
						</li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active" id="report_summary">
							<div class="row reach-graph">
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
											Total Emails Sent
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
											<div class="bounced"></div>
											<div class="rt-desc">
												<p>
													Bounces {{ $history->bounces / count($history->sent_to) * 100 }}%
												</p>
												<p>
													{{ $history->bounces }} Contacts
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
									<div class="rt-prog">
										<div class="progress">
											<div class="progress-bar bg-primary" role="progressbar" style="width: {{ $uniqueOpens / count($history->sent_to) * 100 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<span>
											{{ $uniqueOpens / count($history->sent_to) * 100 }}%
										</span>
									</div>
									<div class="rt-dlvr">
										<div class="dlvr">
											<div class="open"></div>
											<div class="rt-desc">
												<p>
													Unique Opens {{ $uniqueOpens / count($history->sent_to) * 100 }}%
												</p>
												<p>
													{{$uniqueOpens}} Contacts
												</p>
											</div>
										</div>
										<div class="dlvr">
											<div class="clicks"></div>
											<div class="rt-desc">
												<p>
													Unique Clickes {{ $uniqueClicks / count($history->sent_to) * 100 }}%
												</p>
												<p>
													{{$uniqueClicks}} Contacts
												</p>
											</div>
										</div>
										<div class="dlvr">
											<div class="unopen"></div>
											<div class="rt-desc">
												<p>
													Unopened {{ (count($history->sent_to) - $uniqueOpens) / count($history->sent_to) * 100 }}%
												</p>
												<p>
													{{ count($history->sent_to) - $uniqueOpens}} Contacts
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
									<div class="abt-email-container">
										<div class="abt-email">
											<div class="about-mail">
												<div class="abt-mail"> </div>
												<p>
													Email
												</p>
											</div>
											<div class="abt-views">
												<p>
													{{$totalOpens}} Views
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="campaign-graph-holder reach-graph">
									<h5>
										Opens by Time
									</h5>
									<canvas id="myChart" width="400" height="100"></canvas>
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
														Subject:
													</li>
													<li>
														Sender Name:
													</li>
													<li>
														Sender Address:
													</li>
													@if($reply_to_email ?? "")
													<li>
														Reply-to Address:
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
														{{$campaign->subject}}
													</li>
													<li>
														{{$campaign->sender_name}}
													</li>
													<li>
														{{$campaign->sender_email}}
													</li>
													@if($reply_to_email ?? "")
													<li>
														{{$campaign->reply_to_email}}
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
										<li role="presentation">
											<a href="#unopened" aria-controls="unopened" role="tab" data-toggle="tab" id="unopeneddata">Unopened</a>
										</li>
										<li role="presentation">
											<a href="#opened" aria-controls="opened" role="tab" data-toggle="tab" id="opensdata">Opens</a>
										</li>
										<li role="presentation">
											<a href="#clicked" aria-controls="opened" role="tab" data-toggle="tab" id="clicksdata">Clicks</a>
										</li>

										<li role="presentation">
											<a href="#unsubscribed" aria-controls="opened" role="tab" data-toggle="tab" id="unsubscribersdata">Unsubscribers</a>
										</li>
									</ul>
								</div>
								<div class="panel-body">
									<div class="tab-content">
										<div role="tabpanel" class="tab-pane fade in active" id="sent">
											<!-- DATATABLE -->
											<div class="text-right">
												<a target="_blank" href="{{Request::url() . '/download?module=1'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
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
																<th>Email</th>
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
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
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
																<th>Email</th>
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
												<a target="_blank" href="{{Request::url() . '/download?module=22'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
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
																<th>Email</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</div>
											<!-- END DATATABLE -->
										</div>
										<div role="tabpanel" class="tab-pane fade" id="unopened">
											<!-- DATATABLE -->
											<div class="text-right">
												<a target="_blank" href="{{Request::url() . '/download?module=3'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="unopened-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Email</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</div>
											<!-- END DATATABLE -->
										</div>
										<div role="tabpanel" class="tab-pane fade" id="opened">
											<!-- DATATABLE -->
											<div class="text-right">
												<a target="_blank" href="{{Request::url() . '/download?module=4'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="opens-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Email</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</div>
											<!-- END DATATABLE -->
										</div>
										<div role="tabpanel" class="tab-pane fade" id="clicked">
											<!-- DATATABLE -->
											<div class="text-right">
												<a target="_blank" href="{{Request::url() . '/download?module=5'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="clicks-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Email</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</div>
											<!-- END DATATABLE -->
										</div>
										<div role="tabpanel" class="tab-pane fade" id="unsubscribed">
											<!-- DATATABLE -->
											<div class="text-right">
												<a target="_blank" href="{{Request::url() . '/download?module=6'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="unsubscribers-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Email</th>
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
						<div role="tabpanel" class="tab-pane fade" id="click_activities">
							<!-- DATATABLE -->
							<div class="text-right">
								<a target="_blank" href="{{Request::url() . '/download?module=7'}}">
									<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
										<span>Download</span>
									</button>
								</a>
							</div>
							<div class="panel">
								<div class="panel-body">
									<table id="click-activities-datatable" class="table table-hover scroll-handler" style="width:100%">
										<thead>
											<tr>
												<th>#</th>
												<th>First Name</th>
												<th>Last Name</th>
												<th>Email</th>
												<th>Link Clicked</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<!-- END DATATABLE -->
						</div>
						<div role="tabpanel" class="tab-pane fade" id="bounces">
							<!-- DATATABLE -->
							<div class="text-right">
								<a target="_blank" href="{{Request::url() . '/download?module=8'}}">
									<button type="button" class="btn btn-primary btn-fullrounded btn-apply mb-2">
										<span>Download</span>
									</button>
								</a>
							</div>
							<div class="panel">
								<div class="panel-body">
									<table id="bounces-datatable" class="table table-hover scroll-handler" style="width:100%">
										<thead>
											<tr>
												<th>#</th>
												<th>First Name</th>
												<th>Last Name</th>
												<th>Email</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<!-- END DATATABLE -->
						</div>

						<div role="tabpanel" class="tab-pane fade" id="split">
							<div class="panel panel-default">
								<div class="panel-heading panel-heading-nav">
									<ul class="nav nav-tabs">
										<li role="presentation" class="active">
											<a href="#part1" aria-controls="part1" role="tab" data-toggle="tab">Part 1</a>
										</li>
										<li role="presentation" id="splitdata2">
											<a href="#part2" aria-controls="part2" role="tab" data-toggle="tab">Part 2</a>
										</li>
									</ul>
								</div>
								<div class="panel-body">
									<div class="tab-content">
										<div role="tabpanel" class="tab-pane fade in active" id="part1">
											@if($content1[0] == '/')
											<h5> Email Content: </h5>
											<div class="d-flex justify-content-center align-items-center image-tab">
												<img src="{{$content1}}" />;
											</div>
											@else
											<h5> Email Subject: </h5>
											<p> {{$content1}} </p>
											@endif
											<!-- DATATABLE -->
											<div class="text-right space">
												<a target="_blank" href="{{Request::url() . '/download?module=9'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="part1-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Email</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</div>
											<!-- END DATATABLE -->
										</div>
										<div role="tabpanel" class="tab-pane fade" id="part2">
											@if($content2[0] == '/')
											<h5> Email Content: </h5>
											<div class="d-flex justify-content-center align-items-center image-tab">
												<img src="{{$content2}}" />;
											</div>
											@else
											<h5> Email Subject: </h5>
											<p> {{$content2}} </p>
											@endif
											<!-- DATATABLE -->
											<div class="text-right space">
												<a target="_blank" href="{{Request::url() . '/download?module=10'}}">
													<button type="button" class="btn btn-primary btn-fullrounded btn-apply mb-2">
														<span>Download</span>
													</button>
												</a>
											</div>
											<div class="panel">
												<div class="panel-body">
													<table id="part2-datatable" class="table table-hover scroll-handler" style="width:100%">
														<thead>
															<tr>
																<th>#</th>
																<th>First Name</th>
																<th>Last Name</th>
																<th>Email</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.2/chart.min.js" integrity="sha512-tMabqarPtykgDtdtSqCL3uLVM0gS1ZkUAVhRFu1vSEFgvB73niFQWJuvviDyBGBH22Lcau4rHB5p2K2T0Xvr6Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
	console.log(@json($opensDataKeys));
	console.log(@json($opensData));
	const ctx = document.getElementById('myChart');
	const myChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: @json($opensDataKeys),
			datasets: [{
				label: 'Opens',
				data: @json($opensData),
				backgroundColor: ['#24e096', ],
				borderColor: ['#24e096', ],
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
</script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js'></script>
<script>
	$(document).ready(function() {
		var ctx = $("#doughnut");
		var myLineChart = new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: ["Delivered", "Bounced", "Failed"],
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
	var unopened = 0;
	var opens = 0;
	var clicks = 0;
	var unsubscribers = 0;
	var clickstab = 0;
	var bouncetab = 0;
	var splitpart1 = 0;
	var splitpart2 = 0;

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
						d.module = 22;
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

	$('#unopeneddata').on('click', function() {
		if (!unopened) {
			$('#unopened-datatable').dataTable({
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
			unopened = 1;
		}
	})

	$('#opensdata').on('click', function() {
		if (!opens) {
			$('#opens-datatable').dataTable({
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
						d.module = 4;
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
			opens = 1;
		}
	})

	$('#clicksdata').on('click', function() {
		if (!clicks) {
			$('#clicks-datatable').dataTable({
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
						d.module = 5;
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
			clicks = 1;
		}
	})
	$('#unsubscribersdata').on('click', function() {
		if (!unsubscribers) {
			$('#unsubscribers-datatable').dataTable({
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
						d.module = 6;
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
			unsubscribers = 1;
		}
	})

	$('#clickdata').on('click', function() {
		if (!clickstab) {
			$('#click-activities-datatable').dataTable({
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
						d.module = 7;
					}
				},
				columns: [{
						data: 'DT_RowIndex',
						name: 'DT_RowIndex',
						orderable: false,
						searchable: false
					},
					{
						data: 'contact.first_name',
						name: 'contact.first_name'
					},
					{
						data: 'contact.last_name',
						name: 'contact.last_name'
					},
					{
						data: 'contact.email',
						name: 'contact.email'
					},
					{
						data: 'link',
						name: 'link'
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
			clickstab = 1;
		}
	})

	$('#bouncedata').on('click', function() {
		if (!bouncetab) {
			$('#bounces-datatable').dataTable({
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
						d.module = 8;
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
			bouncetab = 1;
		}
	})

	$('#splitdata').on('click', function() {
		if (!splitpart1) {
			$('#part1-datatable').dataTable({
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
						d.module = 9;
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
			splitpart1 = 1;
		}
	})
	$('#splitdata2').on('click', function() {
		if (!splitpart2) {
			$('#part2-datatable').dataTable({
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
						d.module = 10;
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
			splitpart2 = 1;
		}
	})
</script>
@endsection