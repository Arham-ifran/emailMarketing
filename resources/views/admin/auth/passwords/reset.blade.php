@extends('admin.layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="content">
    <div class="header">
        <div class="logo text-center">
            <img src="{{ asset('images/logo-blue-i.svg') }}" alt="">
            <div class="logo-name"><img src="{{ asset('images/logo-text.svg') }}" alt="" class="logo-text-des"></div>
        </div>
        <h4 class="lead">Reset Password</h4>
        <p class="tagline">Enter your email and password below</p>
    </div>
    <form id="reset-password-form" class="form-auth-small" method="POST" action="{{ route('admin.auth.reset-password') }}">

        {{ csrf_field() }}

        @include('admin.messages')

        <div class="form-group">
            <label for="email" class="control-label sr-only">Email <b style="color: red;">*</b></label>
            <input id="email" type="email" class="form-control" name="email" maxlength="191" placeholder="Email address" value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>

        <div class="form-group">
            <label for="password" class="control-label sr-only">Password <b style="color: red;">*</b></label>
            <input id="password" type="password" class="form-control" name="password" minlength="8" maxlength="30" placeholder="Password" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="control-label sr-only">Confirm Password <b style="color: red;">*</b></label>
            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" minlength="8" maxlength="30" placeholder="Confirm Password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Update Password</button>
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

            rules: {
                password_confirmation: {
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
            invalidHandler: function(form) {}
        });
    });
</script>

@endsection