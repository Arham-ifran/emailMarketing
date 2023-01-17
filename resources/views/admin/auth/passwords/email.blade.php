@extends('admin.layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="content">
    <div class="header">
        <div class="logo text-center">
            <a href="/">
                <!-- <img src="{{ asset('images/admin-logo.png') }}" alt="logo" class="img-responsive logo"> -->
                <img src="{{ asset('images/admin-login-logo.png') }}" alt="">
            </a>
        </div>
        <h4 class="lead">Forgot Password</h4>
        <p class="tagline">Enter your email address below</p>
    </div>
    <form id="reset-password-form" class="form-auth-small" method="POST" action="{{ route('admin.auth.send-reset-link-email') }}">

        {{ csrf_field() }}

        @if (session()->get('flash_success'))
        <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {!! session()->get('flash_success') !!}
        </div>
        @endif

        @if (session()->get('flash_danger'))
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {!! session()->get('flash_danger') !!}
        </div>
        @endif

        <div class="form-group">
            <label for="email" class="control-label sr-only">Email <b style="color: red;">*</b></label>
            <input id="email" type="email" class="form-control" name="email" maxlength="191" placeholder="Email address" value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Send Password Reset Link</button>
        <a href="{{ route('admin.auth.login') }}"><button type="button" class="btn btn-primary btn-lg btn-block">Login To Your Account</button></a>
    </form>
</div>
@endsection

@section('js')

<script>
    $(function() {
        $('#reset-password-form').validate({
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
</script>

@endsection