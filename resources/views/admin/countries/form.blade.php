@extends('admin.layouts.app')

@section('title', 'Countries')
@section('sub-title', $action.' Country')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/countries')}}"><i class="fa fa-globe"></i> Countries</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Country</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="countries-form" class="form-horizontal label-left" action="{{url('admin/countries')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $country->id }}" />

							<div class="form-group">
								<label for="name" class="col-sm-3 control-label">Name<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" name="name" maxlength="200" class="form-control" required="" value="{{ ($action == 'Add') ? old('name') : $country->name}}">
								</div>
							</div>

							<div class="form-group">
								<label for="code" class="col-sm-3 control-label">Code<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" name="code" maxlength="200" class="form-control" required="" value="{{ ($action == 'Add') ? old('code') : $country->code}}">
								</div>
							</div>

							<div class="form-group">
								<label for="vat" class="col-sm-3 control-label">VAT (%)<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="number" min="0" max="100" name="vat" class="form-control" required="" value="{{ ($action == 'Add') ? old('vat') : $country->vat}}">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Apply Default VAT</label>
								<div class="col-sm-9">
									@php $apply_default_vat = ($action == 'Add') ? old('apply_default_vat') :
									$country->apply_default_vat @endphp
									<label class="fancy-radio">
										<input name="apply_default_vat" value="1" type="radio" {{ ($apply_default_vat == 1) ? 'checked' : '' }}>
										<span><i></i>Yes</span>
									</label>
									<label class="fancy-radio">
										<input name="apply_default_vat" value="0" type="radio" {{ ($apply_default_vat == 0) ? 'checked' : '' }}>
										<span><i></i>No</span>
									</label>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Status</label>
								<div class="col-sm-9">
									@php $status = ($action == 'Add') ? old('status') : $country->status @endphp
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

							<div class="text-right">
								<a href="{{url('admin/countries')}}">
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
		$('#countries-form').validate({
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