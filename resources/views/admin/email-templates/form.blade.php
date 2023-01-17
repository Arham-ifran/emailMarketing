@extends('admin.layouts.app')

@section('title', 'Email Templates')
@section('sub-title', $action.' Email Template')


@section('content')

<style type="text/css">
	.note-group-select-from-files {
		display: none
	}
</style>

<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/email-templates')}}"><i class="fa fa-envelope"></i> Email Templates</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">

				@if($action == 'Edit' && !empty($email_template->info))
				<div class="alert-info" role="alert" style="padding: 20px;">
					<p>On sending email, following keywords with double curly brackets e.g <strong>@{{keyword}}</strong> will be replaced by their values:</p>
					@foreach(json_decode($email_template->info,true) as $key => $value)
					<li><strong>{{$key}}</strong> : {{$value}} </li>
					@endforeach
				</div>
				<br>
				@endif

				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Email Template</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="email-templates-form" class="form-horizontal label-left" action="{{url('admin/email-templates')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $email_template->id }}" />

							@if($action == 'Add')

							<label for="type" class="control-label">Type<span class="text-red">*</span></label>
							<div class="form-group">
								<div class="col-sm-12">
									<input type="text" name="type" class="form-control" required="" value="{{$email_template->type}}">
								</div>
							</div>

							@endif

							<label for="subject" class="control-label">Subject<span class="text-red">*</span></label>
							<div class="form-group">
								<div class="col-sm-12">
									<input type="text" name="subject" maxlength="250" class="form-control" required="" value="{{$email_template->subject}}">
								</div>
							</div>

							<label for="content" class="control-label">Content<span class="text-red">*</span></label>
							<div class="form-group" id="summernoteDiv">
								<div class="col-sm-12">
									<textarea name="content" class="summernote" required="" rows="15">{{$email_template->content}}</textarea>
									<div id="content-error" class="help-block" style="display:none"></div>
								</div>
							</div>

							<!-- <div class="form-group">
			                	<label class="col-sm-2 control-label">Status</label>
			                	<div class="col-sm-4">
									<label class="fancy-radio">
										<input name="status" value="1" type="radio" {{ ($email_template->status == 1) ? 'checked' : '' }}>
										<span><i></i>Active</span>
									</label>
				                  	<label class="fancy-radio">
				                    	<input name="status" value="0" type="radio" {{ ($email_template->status == 0) ? 'checked' : '' }}>
				                    	<span><i></i>Disable</span>
				                  	</label>
			                	</div>
			              	</div> -->

							<div class="text-right">
								<a href="{{url('admin/email-templates')}}">
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

		// summernote editor
		$('.summernote').summernote({
			height: 500,
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

		$('#email-templates-form').validate({
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
				if ($('.summernote').summernote('isEmpty')) {
					$('#summernoteDiv').addClass('has-error');
					$('#content-error').html('This field is required.').show();
					$('.note-editor.note-frame.panel.panel-default').css('border-color', '#F9354C');
					return false;
				}
				if ($(validator.errorList).length == 0) {
					document.getElementById("page-overlay").style.display = "block";
					return true;
				}
			}
		});
	});
</script>
@endsection