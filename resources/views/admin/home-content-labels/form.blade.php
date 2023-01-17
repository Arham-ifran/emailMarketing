@extends('admin.layouts.app')

@section('title', 'Home Content Labels')
@section('sub-title', $action. ' Home Content Labels')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/home-content-labels')}}"><i class="fa fa-list"></i> Home Content
					Labels</a>
			</li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Home Content Label</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="home-content-label-form" class="form-horizontal label-left"
							action="{{url('admin/home-content-labels')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $model->id }}" />

							<div class="form-group">
								<label for="home_content_id" class="col-sm-3 control-label">Home Contents<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<select class="form-control" name="home_content_id" id="" required>
										<option value="">Select Home Content</option>
										@foreach($home_contents as $home_content)
											@php $selected = ($action == 'Edit' && $home_content->id == $model->home_content_id) ? 'selected' : ''; @endphp
										<option value="{{ $home_content->id }}" {{ $selected }}>
											{{ $home_content->name }}
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
								<a href="{{url('admin/home-content-labels')}}">
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
        $('#home-content-label-form').validate({
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