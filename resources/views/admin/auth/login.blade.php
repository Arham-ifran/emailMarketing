@extends('admin.layouts.auth')

@section('title', 'Login')

@section('content')
<div class="content">
    <div class="header">
        <div class="logo text-center">
            <a href="/">
                <!-- <img src="{{ asset('images/admin-logo.png') }}" alt="logo" class="img-responsive logo"> -->
                <img src="{{ asset('images/admin-login-logo.png') }}" alt="">
            </a>
        </div>
        <h4 class="lead">Log In</h4>
        <p class="tagline">Enter your email and password below</p>
    </div>
    <form id="login-form" class="form-auth-small" method="POST" action="{{ route('admin.auth.loginAdmin') }}">

        {{ csrf_field() }}

        @if ($errors->has('error'))
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ $errors->first('error') }}
        </div>
        @endif

        @if (session()->get('flash_success'))
        <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {!! session()->get('flash_success') !!}
        </div>
        @endif

        <input id="timezone" type="hidden" name="timezone">

        <div class="form-group">
            <label for="signin-email" class="control-label sr-only">Email <b style="color: red;">*</b></label>
            <input id="email" type="email" class="form-control" name="email" placeholder="Email address" required autofocus>
        </div>
        <div class="form-group forget">
            <label for="signin-password" class="control-label sr-only">Password <b style="color: red;">*</b></label>
            <span class="fa fa-fw fa-eye-slash password-field-icon toggle-password"></span>
            <a href="{{ route('admin.auth.forgot-password') }}" class="forget-link">Forgot password?</a>
            <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
        </div>
        <!-- <div class="form-group clearfix">
            <label class="fancy-checkbox element-left custom-bgcolor-blue">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="text-muted">Remember me</span>
            </label>
        </div> -->
        <button type="submit" class="btn btn-primary btn-lg btn-block">Log In</button>
    </form>
</div>
@endsection

@section('js')

<script>
    $(function() {
        $('#login-form').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,

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
            invalidHandler: function(form) {}
        });
    });

    $(window).load(function() {
        $('#timezone').val(Intl.DateTimeFormat().resolvedOptions().timeZone);
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