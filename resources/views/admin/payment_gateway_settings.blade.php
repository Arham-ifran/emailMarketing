@extends('admin.layouts.app')

@section('title', 'Payment Settings')
@section('sub-title', 'Edit Settings')

@section('content')
<div class="main-content">
  <div class="content-heading clearfix">

    <ul class="breadcrumb">
      <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
      <li>Payment Settings</li>
    </ul>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel">
          <div class="panel-heading">
            <h3 class="panel-title">Payment Settings</h3>
          </div>
          <div class="panel-body">
            @include('admin.messages')
            <form id="settings-form" class="form-horizontal label-left" action="{{url('admin/payment-settings')}}" enctype="multipart/form-data" method="POST">
              {{ csrf_field() }}

              <h4 class="heading">Mollie Mode & Status</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Mode</label>
                <div class="col-sm-4">
                  <label class="fancy-radio">
                    <input name="mollie_mode" value="sandbox" type="radio" {{ ($model->mollie_mode == 'sandbox') ? 'checked' : '' }}>
                    <span><i></i>Sandbox</span>
                  </label>
                  <label class="fancy-radio">
                    <input name="mollie_mode" value="live" type="radio" {{ ($model->mollie_mode == 'live') ? 'checked' : '' }}>
                    <span><i></i>Live</span>
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Status</label>
                <div class="col-sm-4">
                  <label class="fancy-radio">
                    <input name="mollie_status" value="1" type="radio" {{ ($model->mollie_status == 1) ? 'checked' : '' }}>
                    <span><i></i>Active</span>
                  </label>
                  <label class="fancy-radio">
                    <input name="mollie_status" value="0" type="radio" {{ ($model->mollie_status == 0) ? 'checked' : '' }}>
                    <span><i></i>Disable</span>
                  </label>
                </div>
              </div>

              <h4 class="heading">Mollie Sandbox</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Api Key</label>
                <div class="col-sm-9">
                  <input type="text" name="mollie_sandbox_api_key" maxlength="250" class="form-control" value="{{$model->mollie_sandbox_api_key}}" required>
                </div>
              </div>

              <h4 class="heading">Mollie Live</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Api Key</label>
                <div class="col-sm-9">
                  <input type="text" name="mollie_live_api_key" maxlength="250" class="form-control" value="{{$model->mollie_live_api_key}}">
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
@endsection

@section('js')
<script>
  $(function() {
    $('#settings-form').validate({
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