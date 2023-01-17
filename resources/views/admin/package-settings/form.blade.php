@extends('admin.layouts.app')

@section('title', 'Pay As You Go Package Settings')
@section('sub-title', $action.' Package Setting')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/package-settings')}}"><i class="fa fa-list"></i>Pay As You Go Package Settings</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Package Setting</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="package-settings-form" class="form-horizontal label-left" action="{{url('admin/package-settings')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $package_setting->id }}" />

							<!-- <div class="form-group">
								<label class="col-sm-3 control-label">Module</label>
								<div class="col-sm-9">
									<label class="fancy-radio">
										<input name="module" value="1" type="radio" {{ (!empty($package_setting->module) && $package_setting->module == 1) ? 'checked' : '' }} {{ $action == 'Add' ? '' : 'disabled' }}>
										<span><i></i>Contacts</span>
									</label>
									<label class="fancy-radio">
										<input name="module" value="2" type="radio" {{ (!empty($package_setting->module) && $package_setting->module == 2) ? 'checked' : '' }} {{ $action == 'Add' ? '' : 'disabled' }}>
										<span><i></i>Emails</span>
									</label>
									<label class="fancy-radio">
										<input name="module" value="3" type="radio" {{ (!empty($package_setting->module) && $package_setting->module == 3) ? 'checked' : '' }} {{ $action == 'Add' ? '' : 'disabled' }}>
										<span><i></i>SMS</span>
									</label>
								</div>
							</div> -->

							<div class="form-group">
								<label for="name" class="col-sm-3 control-label">Name</label>
								<div class="col-sm-9">
									<input type="text" name="name" maxlength="200" class="form-control" required="" value="{{$package_setting->name}}">
								</div>
							</div>

							<div class="form-group">
								<label for="info" class="col-sm-3 control-label">Info</label>
								<div class="col-sm-9">
									<input type="text" name="info" maxlength="200" class="form-control" value="{{$package_setting->info}}">
								</div>
							</div>


							<div class="form-group caldav-cardav-dependant voucher-dependant">
								<label for="start_range" class="col-sm-3 control-label">Start Range</label>
								<div class="col-sm-9">
									<input type="number" name="start_range" class="form-control" value="{{$package_setting->start_range}}" disabled>
								</div>
							</div>

							<div class="form-group caldav-cardav-dependant voucher-dependant">
								<label for="end_range" class="col-sm-3 control-label">End Range</label>
								<div class="col-sm-9">
									<input type={{ ($package_setting->id == 3 || $package_setting->id == 6) ? 'text' : 'number' }} name="end_range" class="form-control" value={{ ($package_setting->id == 3 || $package_setting->id == 6) ? '∞' : $package_setting->end_range }} {{ $package_setting->id == 3 || $package_setting->id == 6 ? 'disabled' : '' }}>
								</div>
							</div>

							<div class="form-group">
								<label for="price_without_vat" class="col-sm-3 control-label">Price Per Unit</label>
								<div class="col-sm-9">
									<input type="number" name="price_without_vat" class="form-control" value="{{$package_setting->price_without_vat}}">
								</div>
							</div>

							<!-- <div class="form-group">
								<label for="price_with_vat" class="col-sm-3 control-label">Price Per Unit (with VAT)</label>
								<div class="col-sm-9">
									<input type="number" name="price_with_vat" class="form-control" value="{{$package_setting->price_with_vat}}">
								</div>
							</div> -->

							<!-- <div class="form-group">
								<label class="col-sm-3 control-label">Voucher Settings</label>
								<div class="col-sm-9">
									<label class="fancy-radio">
										<input name="is_voucher_setting" value="1" type="radio" {{ (!empty($package_setting->is_voucher_setting) && $package_setting->is_voucher_setting == 1) ? 'checked' : '' }}>
										<span><i></i>Yes</span>
									</label>
									<label class="fancy-radio">
										<input name="is_voucher_setting" value="0" type="radio" {{ (empty($package_setting->is_voucher_setting) || $package_setting->is_voucher_setting == 0) ? 'checked' : '' }}>
										<span><i></i>No</span>
									</label>
								</div>
							</div> -->

							<!-- <div class="form-group" style="display:none">
								<label class="col-sm-3 control-label">Status</label>
								<div class="col-sm-9">
									<label class="fancy-radio">
										<input name="status" value="1" type="radio" {{ ($package_setting->status == 1) ? 'checked' : '' }}>
										<span><i></i>Active</span>
									</label>
									<label class="fancy-radio">
										<input name="status" value="0" type="radio" {{ ($package_setting->status == 0) ? 'checked' : '' }}>
										<span><i></i>Disable</span>
									</label>
								</div>
							</div> -->

							<div class="text-right">
								<a href="{{url('admin/package-settings')}}">
									<button type="button" class="btn cancel  btn-fullrounded">
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
	$(function() {
		$('#package-settings-form').validate({
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

		// $('body').on('change', '[name="is_voucher_setting"]', function() {
		// 	if (this.value == '1') {
		// 		$('.voucher-dependant').find('input').val('');
		// 		$('.is_caldav_cardav_migration_no').trigger('click');
		// 		// $('input[type=radio][name=is_caldav_cardav_migration]').val('0');
		// 		//$("input[name='is_caldav_cardav_migration']").attr("checked", true).val(0);
		// 		$('.voucher-setting-prior').show();
		// 		$('.voucher-dependant').hide();
		// 	} else {
		// 		$('.voucher-dependant').show();
		// 		// if($('input[type=radio][name="is_caldav_cardav_migration"]').val() == 1)
		// 		// {
		// 		// 	$('.caldav-cardav-dependant').hide();
		// 		// }
		// 	}
		// });

	});
</script>
@endsection