@extends('admin.layouts.app')

@section('title', 'Users')
@section('sub-title', $action.' User')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/users')}}"><i class="fa fa-user"></i>Users</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} User</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="users-form" class="form-horizontal label-left" action="{{url('admin/users')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $user->id }}" />

							<h4 class="heading">Basic Information</h4>

							<div class="form-group">
								<label for="name" class="col-sm-3 control-label">Name<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" name="name" maxlength="100" class="form-control" required="" value="{{ ($action == 'Add') ? old('name') : $user->name}}">
								</div>
							</div>

							<div class="form-group">
								<label for="email" class="col-sm-3 control-label">Email<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="email" name="email" maxlength="100" class="form-control" value="{{ ($action == 'Add') ? old('email') : $user->email}}" required="">
								</div>
							</div>

							<div class="form-group">
								<label for="country" class="col-sm-3 control-label">Country<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<select class="form-control" name="country_id" id="country" required="">
										@foreach ($countries as $country)
										<option value="{{$country->id}}" {{$country->id == $user->country_id  ? 'selected' : ''}}>
											{{$country->name}}
										</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="country" class="col-sm-3 control-label">Timezone<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<select class="form-control" name="timezone" id="country" required="">
										@foreach ($timezones as $zone)
										<option value="{{$zone->name}}" {{$zone->name == $user->timezone  ? 'selected' : ''}}>
											{{$zone->name}}
										</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="email" class="col-sm-3 control-label">City</label>
								<div class="col-sm-9">
									<input type="text" name="city" maxlength="100" class="form-control" value="{{ ($action == 'Add') ? old('city') : $user->city}}">
								</div>
							</div>

							<div class="form-group">
								<label for="email" class="col-sm-3 control-label">Street</label>
								<div class="col-sm-9">
									<input type="text" name="street" maxlength="100" class="form-control" value="{{ ($action == 'Add') ? old('street') : $user->street}}">
								</div>
							</div>

							<div class="form-group">
								<label for="email" class="col-sm-3 control-label">Zip Code</label>
								<div class="col-sm-9">
									<input type="text" name="zip_code" maxlength="100" class="form-control" value="{{ ($action == 'Add') ? old('zip_code') : $user->zip_code}}">
								</div>
							</div>

							<!-- <div class="form-group">
								<label for="timezone" class="col-sm-3 control-label">Timezone*</label>
								<div class="col-sm-9">
									<select class="form-control" name="timezone" id="timezone" required="">
										@foreach ($timezones as $timezone)
										<option value="{{$timezone->name}}" {{$timezone->name == $user->timezone  ? 'selected' : ''}}>
											{{$timezone->name}}
										</option>
										@endforeach
									</select>
								</div>
							</div> -->

							{{-- <div class="form-group">
								<label for="language" class="col-sm-3 control-label">Language*</label>
								<div class="col-sm-9">
									<select class="form-control" name="language" required="">
                                        @foreach ($languages as $language)
                                            <option value="{{$language->code}}"
							{{$language->code == $user->language  ? 'selected' : ''}} >{{$language->name}}</option>
							@endforeach
							</select>
					</div>
				</div> --}}

				<div class="form-group">
					<label class="col-sm-3 control-label">Status</label>
					<div class="col-sm-9">
						@php $status = ($action == 'Add') ? old('status') : $user->status @endphp
						<label class="fancy-radio">
							<input name="status" value="1" type="radio" {{ ($status == 1) ? 'checked' : '' }}>
							<span><i></i>Active</span>
						</label>
						<label class="fancy-radio">
							<input name="status" value="0" type="radio" {{ ($status == 0) ? 'checked' : '' }}>
							<span><i></i>Disable</span>
						</label>
						@if($action == 'Edit')
						<label class="fancy-radio">
							<input name="status" value="2" type="radio" {{ ($status == 2) ? 'checked' : '' }}>
							<span><i></i>Unverified</span>
						</label>
						<label class="fancy-radio">
							<input name="status" value="3" type="radio" {{ ($status == 3) ? 'checked' : '' }}>
							<span><i></i>Deleted</span>
						</label>
						@endif
					</div>
				</div>

				<hr>
				<h4 class="heading">
					Password & Confirm Password
					@if($action == 'Edit')
					<a href="{{url('admin/users/send-password/'.Hashids::encode($user->id))}}" class="pull-right">
						<button type="button" class="btn btn-primary btn-sm btn-fullrounded">
							<i class="fa fa-paper-plane"></i><span>Send Password</span>
						</button>
					</a>
					@endif
				</h4>

				<div class="form-group">
					<label for="password" class="col-sm-3 control-label">Password</label>
					<div class="col-sm-9">
						<span class="fa fa-fw fa-eye password-field-icon toggle-password"></span>
						<input type="password" name="password" id="password" minlength="8" maxlength="30" class="password form-control" value="{{$user->original_password}}" required="">
					</div>
				</div>

				<div class="form-group">
					<label for="confirm_password" class="col-sm-3 control-label">Confirm Password</label>
					<div class="col-sm-9">
						<span class="fa fa-fw fa-eye password-field-icon toggle-password"></span>
						<input type="password" name="confirm_password" class="password form-control" value="{{$user->original_password}}" required="">
					</div>
				</div>

				<div class="text-right">
					<a href="{{url('admin/users')}}">
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
	$(function() {

		$('#country').select2({
			placeholder: 'Select a Country',
			allowClear: true
		});

		$('#timezone').select2({
			placeholder: 'Select a Timezone',
			allowClear: true
		});

		$('#users-form').validate({
			errorElement: 'div',
			errorClass: 'help-block',
			focusInvalid: true,

			rules: {
				password: {
					passwordCheck: true
				},
				confirm_password: {
					equalTo: "#password"
				},
				email: {
					emailCheck: true
				}
			},

			messages: {
				password: {
					passwordCheck: "Must contain at least one  number and one uppercase and lowercase letter, and at least 8 or more characters",
				},
				email: {
					emailCheck: "Please enter a valid email address."
				}
			},

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
		$.validator.addMethod("passwordCheck", function(value) {
			return /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/.test(value)
		});
		$.validator.addMethod("emailCheck", function(value) {
			return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value)
		});
	});

	$(".toggle-password").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
		var input = $(this).siblings('input');
		if (input.attr("type") == "password") {
			input.attr("type", "text");
		} else {
			input.attr("type", "password");
		}
	});
</script>
@endsection