@extends('admin.layouts.app')

@section('title', 'CMS Page Labels')
@section('sub-title', $action. ' CMS Page Labels')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/cms-page-labels')}}"><i class="fa fa-file-text-o"></i> CMS Page Labels</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} CMS Page Label</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="cms-page-label-form" class="form-horizontal label-left"
							action="{{url('admin/cms-page-labels')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $model->id }}" />

							<div class="form-group">
								<label for="cms_page_id" class="col-sm-3 control-label">CMS Pages<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<select class="form-control" name="cms_page_id" id="" required>
										<option value="">Select CMS Page</option>
										@foreach($cms_pages as $cms_page)
											@php $selected = ($action == 'Edit' && $cms_page->id == $model->cms_page_id)
											? 'selected' : ''; @endphp
											<option value="{{ $cms_page->id }}" {{ $selected }}>{{ $cms_page->title }}
											</option>
										@endforeach
									</select>
								</div>
							</div>
							<hr>
							<div id="labels">
								<div class="form-group">
									<label for="label" class="col-sm-3 control-label">Label<span class="text-red">*</span></label>
									<div class="col-sm-9">
										<input type="text" name="label[]" class="form-control" required=""
											value="{{ ($action == 'Edit') ? $model->label : ''}}">
									</div>
								</div>
								<div class="form-group">
									<label for="value" class="col-sm-3 control-label">Value<span class="text-red">*</span></label>
									<div class="col-sm-9">
										<textarea name="value[]" class="form-control" required=""
											rows="5">{{ ($action == 'Edit') ? $model->value : ''}}</textarea>
									</div>
								</div>
							</div>

							@if($action == 'Add')
							<hr>
							<div class="form-group">
								<div class="col-sm-12">
									<button type="button" id="add-label" class="pull-right btn btn-success">
										<i class="fa fa-plus"></i>
									</button>
								</div>
							</div>
							@endif

							<div class="text-right">
								<a href="{{url('admin/cms-page-labels')}}">
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

<div id="dynamic_label_fields" style="display: none;">
	<div>
		<hr>
		<div class="form-group">
			<label for="label" class="col-sm-3 control-label">Label</label>
			<div class="col-sm-9">
				<input type="text" name="label[]" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="value" class="col-sm-3 control-label">Value</label>
			<div class="col-sm-9">
				<textarea name="value[]" class="form-control" rows="5"></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<button type="button" class="pull-right btn btn-danger remove_label"><i
						class="fa fa-times"></i></button>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js')
<script>
	$(function(){
        $('#cms-page-label-form').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: true,
            
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

		$('#add-label').on('click',function(){
        	$('#labels').append($('#dynamic_label_fields').html());
        });

        $(document).on('click', '.remove_label', function(){
        	$(this).parent().parent().parent().remove();
        });
    });

</script>
@endsection