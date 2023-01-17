@extends('admin.layouts.app')

@section('title', 'Admin Users')
@section('sub-title', $action.' Admin User')

@section('content')
<div class="main-content">
	<div class="content-heading clearfix">
		
		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/admins')}}"><i class="fa fa-user"></i>Admin Users</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Admin User</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="admins-form" class="form-horizontal label-left" action="{{url('admin/admins')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
              				<input name="id" type="hidden" value="{{ $admin->id }}"/>

			              	<h4 class="heading">Basic Information</h4>

			              	<div class="form-group">
								<label for="name" class="col-sm-3 control-label">Name<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" name="name" maxlength="100" class="form-control" required="" value="{{ ($action == 'Add') ? old('name') : $admin->name}}">
								</div>
							</div>

							<div class="form-group">
								<label for="name" class="col-sm-3 control-label">Email<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="email" name="email" maxlength="100" class="form-control" value="{{ ($action == 'Add') ? old('email') : $admin->email}}" required="">
								</div>
							</div>

              				<div class="form-group">
								<label for="role" class="col-sm-3 control-label">Role<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<select class="form-control" name="role_id" required="">
                                        @foreach ($roles as $role)
                                            <option value="{{$role->id}}" {{$role->id == $admin->role_id  ? 'selected' : ''}} >{{$role->name}}</option>
                                        @endforeach
                                    </select>
								</div>
							</div>

							<div class="form-group">
			                	<label class="col-sm-3 control-label">Status</label>
			                	<div class="col-sm-9">
									@php $status = ($action == 'Add') ? old('status') : $admin->status @endphp
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

							<hr>
			              	<h4 class="heading">Password & Confirm Password</h4>

							<div class="form-group">
								<label for="password" class="col-sm-3 control-label">Password</label>
								<div class="col-sm-9">
									<span class="fa fa-fw fa-eye password-field-icon toggle-password"></span>
									<input type="password" name="password" id="password" minlength="8" maxlength="30" class="password form-control" value="{{$admin->original_password}}" required="">
								</div>
							</div>

							<div class="form-group">
								<label for="confirm_password" class="col-sm-3 control-label">Confirm Password</label>
								<div class="col-sm-9">
									<span class="fa fa-fw fa-eye password-field-icon toggle-password"></span>
									<input type="password" name="confirm_password" class="password form-control" value="{{$admin->original_password}}" required="">
								</div>
							</div>

							<div class="text-right">
								<a href="{{url('admin/admins')}}">
						        	<button type="button" class="btn cancel btn-fullrounded">
						          		<span>Cancel</span>
						        	</button>
						      	</a>

								<button type="submit" class="btn btn-primary  btn-fullrounded">
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
    $(function(){

        $('#admins-form').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: true,

            rules: {
            	password: {
                    passwordCheck:true
                },
                confirm_password: {
                  	equalTo: "#password"
                },
                email: {
                    emailCheck:true
                },
            },

            messages: {
                password: {
                    passwordCheck: "Allowed Characters: a-z A-Z 0-9 !@#$%^& with at least 1 lowercase character, 1 uppercase character, 1 numeric character, 1 special character and must be eight characters or longer",
                },
                email: {
                	emailCheck: "Please enter a valid email address."
                }
            },
            
            highlight: function (e) {
            	$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },
            success: function (e) {
	            $(e).closest('.form-group').removeClass('has-error');
	            $(e).remove();
            },
            errorPlacement: function (error, element) {
	            if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
		            var controls = element.closest('div[class*="col-"]');
		            if (controls.find(':checkbox,:radio').length > 1)
		                    controls.append(error);
		            else
	                    error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
	            } 
	            else if (element.is('.select2')) {
	            	error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
	            } 
	            else if (element.is('.chosen-select')) {
	            	error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
	            } 
	            else
                    error.insertAfter(element);
            },
			invalidHandler: function (form,validator) {
            	$('html, body').animate({
		            scrollTop: $(validator.errorList[0].element).offset().top - scrollTopDifference
		        }, 500);
            },
            submitHandler: function (form,validator) {
            	if($(validator.errorList).length == 0)
            	{
            		document.getElementById("page-overlay").style.display = "block";
            		return true;
            	}
            }
        });
        $.validator.addMethod("passwordCheck", function(value) {
           	return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/.test(value)
        });
        $.validator.addMethod("emailCheck", function(value) {
           	return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value)
        });
    });

	$(".toggle-password").click(function() 
	{
		$(this).toggleClass("fa-eye fa-eye-slash");
		var input = $(this).siblings('input');
		if (input.attr("type") == "password") 
		{
			input.attr("type", "text");
		}
		else 
		{
			input.attr("type", "password");
		}
	});
	
</script>

@endsection
