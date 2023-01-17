@extends('admin.layouts.app')

@section('title', 'Packages')
@section('sub-title', $action.' Package')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/packages')}}"><i class="fa fa-list"></i> Packages</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Package</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="packages-form" class="form-horizontal label-left" action="{{url('admin/packages')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $package->id }}" />

							<div class="form-group">
								<label for="title" class="col-sm-3 control-label">Title<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" name="title" maxlength="100" class="form-control" required="" value="{{ ($action == 'Add') ? old('title') : $package->title}}">
								</div>
							</div>

							<div class="form-group">
								<label for="sub_title" class="col-sm-3 control-label">Sub-Title<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" name="sub_title" maxlength="100" class="form-control" required="" value="{{ ($action == 'Add') ? old('sub_title') : $package->sub_title}}">
								</div>
							</div>

							<div class="form-group" style="display: {{ in_array($package->id,[1,2]) ? 'none' : '' }}">
								<label for="monthly_price" class="col-sm-3 control-label">Monthly Price ({{ config('constants.currency')['symbol'] }})<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="number" min="0" max="10000" name="monthly_price" class="form-control" required="" value="{{ ($action == 'Add') ? old('monthly_price') : $package->monthly_price}}">
								</div>
							</div>

							<div class="form-group" style="display: {{ in_array($package->id,[1,2]) ? 'none' : '' }}">
								<label for="yearly_price" class="col-sm-3 control-label">Yearly Price ({{ config('constants.currency')['symbol'] }})<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="number" min="0" max="10000" name="yearly_price" class="form-control" required="" value="{{ ($action == 'Add') ? old('yearly_price') : $package->yearly_price}}">
								</div>
							</div>

							<div class="form-group hidden">
								<label for="icon" class="col-sm-3 control-label">Upload Icon</label>
								<div class="col-sm-9">
									<input type="file" name="icon" class="form-control" onchange="readURL(this,'icon',['svg+xml'],'icon-error')">
									<span id="icon-error" style="display:none;color:#f36363;"></span>
									<br>
									<span class="label label-info">Note:</span> Recommended icon resolution is 100x90 pixels.
									<br><br>
									<div style="width: 100px; height: auto">
										<img src="{{checkImage(asset('storage/packages/' . $package->icon),'placeholder.png')}}" class="img-responsive" alt="Icon" id="icon">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="description" class="col-sm-3 control-label">Description<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<textarea name="description" class="summernote" required="" rows="3">{{ ($action == 'Add') ? old('description') : $package->description}}</textarea>
								</div>
							</div>

							<div class="form-group" style="display: {{ ($package->id == 2) ? 'none' : '' }}">
								<label class="col-sm-3 control-label">Status</label>
								<div class="col-sm-9">
									@php $status = ($action == 'Add') ? old('status') : $package->status @endphp
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

							<div class="form-group">
								<label class="col-sm-3 control-label">Recommended</label>
								<div class="col-sm-9">
									@php $recommended = ($action == 'Add') ? old('recommended') : $package->recommended @endphp
									<label class="fancy-radio">
										<input name="recommended" value="1" type="radio" {{ ($recommended == 1) ? 'checked' : '' }}>
										<span><i></i>True</span>
									</label>
									<label class="fancy-radio">
										<input name="recommended" value="0" type="radio" {{ ($recommended == 0) ? 'checked' : '' }}>
										<span><i></i>False</span>
									</label>
								</div>
							</div>

							<hr>
							<h4 class="heading">Features</h4>
							@foreach($packageFeatures as $packageFeature)
							@php
							$checked = "";
							$count = 1;
							@endphp

							@if($action == 'Add' AND !empty(old('package_features')) AND in_array($packageFeature->id, old('package_features')))
							@php $checked = "checked"; @endphp
							@elseif($action == 'Edit' AND !empty($package->linkedFeatures))
							@php
							$arr = $package->linkedFeatures->pluck('count','feature_id')->toArray();

							if(isset($arr[$packageFeature->id]))
							{
							$checked = "checked";
							$count = $arr[$packageFeature->id];
							}
							@endphp
							@endif
							<div class="form-group features">
								<label class="fancy-checkbox custom-bgcolor-blue col-sm-8 col-xs-12">
									<input name="package_features[]" type="checkbox" value="{{$packageFeature->id}}" {{$checked}}>
									<span>{{$packageFeature->name}}</span>
								</label>
								<div class="col-sm-4 col-xs-12">
									@if($packageFeature->count)
									<input type="number" value="{{ $count }}" min="1" max="10000" name="package_features_count[]" class="form-control">
									@else
									<input type="hidden" value="0" name="package_features_count[]" class="form-control">
									@endif
								</div>
							</div>
							@endforeach

							<br><br>

							<div class="text-right">
								<a href="{{url('admin/packages')}}">
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
	$(function() {
		$('.summernote').summernote({
			height: 300,
			focus: true,
			onpaste: function() {
				alert('You have pasted something to the editor');
			},
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['insert', ['link', 'picture', 'video']],
				['para', ['ol', 'ul', 'paragraph']],
				['table', ['table']],
				['view', ['undo', 'redo', 'fullscreen', 'codeview']]
			]
		});

		$('#packages-form').validate({
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