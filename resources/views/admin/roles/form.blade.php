@extends('admin.layouts.app')

@section('title', 'Roles')
@section('sub-title', $action.' Role')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/roles')}}"><i class="fa fa-user-secret"></i> Roles</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Role</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="roles-form" class="form-horizontal label-left" action="{{url('admin/roles')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $role->id }}" />

							<div class="form-group">
								<label for="name" class="col-sm-3 control-label">Name<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" name="name" maxlength="100" class="form-control" required="" value="{{ ($action == 'Add') ? old('name') : $role->name}}">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Status</label>
								<div class="col-sm-9">
									@php $status = ($action == 'Add') ? old('status') : $role->status @endphp
									<label class="fancy-radio">
										<input name="status" value="1" type="radio" {{ ($status == 1) ? 'checked' : '' }}>
										<span><i></i>Active</span>
									</label>
									<label class="fancy-radio">
										<input name="status" value="0" type="radio" {{ ($status == 0) ? 'checked' : '' }}>
										<span><i></i>Disable</span>
									</label>
								</div>
							</div>

							@if($role->id != 1)
							<br>
							<h4 class="heading">To give permission on specific module.</h4><br>

							<div class="row">
								<!-- <h4 class="heading col-sm-12">Select All</h4> -->
								<div class="col-sm-3">
									<label class="fancy-checkbox custom-bgcolor-darkblue">
										<input type="checkbox" name="all_checkbox" id="all_checkbox" value="1" onclick="select_all()">
										<span>Select All Permissions</span>
									</label>
								</div>
							</div>

							<br>

							@foreach(rights() as $module => $rights)
							<div class="row">
								<h4 class="heading col-sm-12">{{$module}}</h4>
								@foreach($rights as $right)
								<?php
								$checked = '';
								if (!empty($role->right_ids) && in_array($right->id, explode(',', $role->right_ids))) {
									$checked = "checked";
								}
								?>
								<div class="col-sm-3">
									<label class="fancy-checkbox custom-bgcolor-darkblue">
										<input type="checkbox" name="right_ids[]" class="mark_these" <?php echo $checked; ?> value="{{$right->id}}">
										<span>{{$right->right_name}}</span>
									</label>
								</div>
								@endforeach
							</div>
							<hr>
							@endforeach
							@endif

							<div class="text-right">
								<a href="{{url('admin/roles')}}">
									<button type="button" class="btn cancel btn-fullrounded">
										<span>Cancel</span>
									</button>
								</a>

								<button type="submit" class="btn btn-primary btn-fullrounded">
									<span>Save</span>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script>
	function select_all() {
		const mark = document.getElementById("all_checkbox").checked;
		// console.log(mark);
		var boxes = document.getElementsByClassName("mark_these");

		Array.prototype.forEach.call(boxes, function(el) {
			el.checked = mark;
		});
	}

	$(function() {
		$('#roles-form').validate({
			errorElement: 'div',
			errorClass: 'help-block',
			focusInvalid: true,

			highlight: function(e) {
				$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
			},
			success: function(e) {
				$(e).closest('.form-group').removeClass('has-error');
				$(e).remove();
			},
			errorPlacement: function(error, element) {
				if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
					var controls = element.closest('div[class*="col-"]');
					if (controls.find(':checkbox,:radio').length > 1)
						controls.append(error);
					else
						error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
				} else if (element.is('.select2')) {
					error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
				} else if (element.is('.chosen-select')) {
					error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
				} else
					error.insertAfter(element);
			},
			invalidHandler: function(form, validator) {
				$('html, body').animate({
					scrollTop: $(validator.errorList[0].element).offset().top - scrollTopDifference
				}, 500);
			},
			submitHandler: function(form, validator) {
				if ($(validator.errorList).length == 0) {
					document.getElementById("page-overlay").style.display = "block";
					return true;
				}
			}
		});
	});
</script>
@endsection