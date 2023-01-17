@extends('admin.layouts.app')

@section('title', 'Home Contents')
@section('sub-title', $action.' Content')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">

		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/home-contents')}}"><i class="fa fa-file-o"></i> Home Contents</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Content</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="home-contents-form" class="form-horizontal label-left" action="{{url('admin/home-contents')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $model->id }}" />

							<div class="form-group">
								<label for="name" class="col-sm-2 control-label">Name<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" name="name" maxlength="250" class="form-control" required="" value="{{ ($action == 'Add') ? old('name') : $model->name}}">
								</div>
							</div>

							<div class="form-group" id="summernoteDiv">
								<label for="description" class=" col-sm-2 control-label">Description<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<textarea name="description" class="summernote" required="" rows="15" value="{{ ($action == 'Add') ? old('description') : $model->description}}">{{ ($action == 'Add') ? old('description') : $model->description}}</textarea>
									<div id="content-error" class="help-block" style="display:none"></div>
								</div>
							</div>

							<div class="form-group">
								<label for="image" class="col-sm-2 control-label">Upload Image</label>
								<div class="col-sm-10">
									<input type="file" name="image" class="form-control" onchange="readURL(this,'image',['jpeg','jpg','png'],'image-error','image')">
									<span id="image-error" style="display:none;color:#f36363;"></span>
									<br>
									<span class="label label-info">Note:</span> Recommended image resolution is 540x440 pixels.
									<br><br>
									<div style="width: 540px; height: auto">
										<img src="{{checkImage(asset('storage/home-contents/' . $model->image),'placeholder.png')}}" class="img-responsive" alt="" id="image">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label">Status</label>
								<div class="col-sm-10">
									@php $status = ($action == 'Add') ? old('status') : $model->status @endphp
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
								<a href="{{url('admin/home-contents')}}">
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

		$('#home-contents-form').validate({
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