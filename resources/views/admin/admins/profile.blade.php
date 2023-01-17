@extends('admin.layouts.app')

@section('title', 'Profile')
@section('sub-title', 'Your Account Information')

@section('content')
<div class="main-content">
    <div class="content-heading clearfix">

    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Profile</h3>
                    </div>
                    <div class="panel-body">
                        @include('admin.messages')
                        <h4 class="heading">Basic Information</h4>
                        <form id="profile-form" class="form-horizontal label-left" action="{{ route('admin.update-profile') }}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="firstname" class="col-sm-3 control-label">Name<span class="text-red">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" maxlength="100" class="form-control" required="" value="{{Auth::user()->name}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">Email*</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" maxlength="100" class="form-control" required="" value="{{Auth::user()->email}}" readonly="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">Profile Image</label>
                                <div class="col-sm-9">
                                    <input type="file" name="profile_image" class="form-control" onchange="readURL(this,'profile_image',['jpg','png','jpeg','svg+xml'],'profile_image-error','image')">
                                    <span id="profile_image-error" style="display:none;color:#f36363;"></span>
                                    <br>
                                    <span class="label label-info">Note:</span> Recommended image resolution is 100x100 pixels.
                                    <br><br>
                                    <div style="width: 100px; height: auto">
                                        <img src="{{checkImage(asset('storage/admins/profile-images/' . Auth::user()->profile_image),'user.png')}}" class="img-responsive" alt="Avatar" id="profile_image">
                                    </div>
                                </div>
                            </div>
                            <h4 class="heading">Password & Confirm Password</h4>
                            <div class="form-group profile-pswrd-alignment">
                                <label for="password" class="col-sm-3 control-label">Password</label>
                                <div class="col-sm-9">
                                    <span class="fa fa-fw fa-eye password-field-icon toggle-password"></span>
                                    <input type="password" name="password" id="password" minlength="8" maxlength="30" class="password form-control" value="{{Auth::user()->original_password}}" required="">
                                </div>
                            </div>
                            <div class="form-group profile-pswrd-alignment">
                                <label for="confirm_password" class="col-sm-3 control-label">Confirm Password</label>
                                <div class="col-sm-9">
                                    <span class="fa fa-fw fa-eye password-field-icon toggle-password"></span>
                                    <input type="password" name="confirm_password" class="password form-control" value="{{Auth::user()->original_password}}" required="">
                                </div>
                            </div>

                            <div class="text-right">
                                <a href="{{url('admin')}}">
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
</div>
@endsection

@section('js')
<script>
    $(function() {
        $('#profile-form').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,

            rules: {
                confirm_password: {
                    equalTo: "#password"
                },
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