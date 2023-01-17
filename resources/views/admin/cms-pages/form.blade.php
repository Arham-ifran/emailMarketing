@extends('admin.layouts.app')

@section('title', 'CMS Pages')
@section('sub-title', $action.' CMS Page')

@section('content')

<style type="text/css">
	.note-group-select-from-files {display: none}
</style>

<div class="main-content">
	<div class="content-heading clearfix">
		
		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/cms-pages')}}"><i class="fa fa-file-text-o"></i> CMS Pages</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} CMS Page</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="cms-page-form" class="form-horizontal label-left" action="{{url('admin/cms-pages')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
              				<input name="id" type="hidden" value="{{ $cms_page->id }}"/>

							<div class="form-group">
								<label for="title" class="col-sm-2 control-label">Title<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" id="title" name="title" maxlength="200" class="form-control" required="" value="{{ ($action == 'Add') ? old('title') : $cms_page->title}}" {{ in_array($cms_page->id, [1,2,3,4]) ? "readonly" : ""}}>
								</div>
							</div>

							<div class="form-group">
								<label for="name" class="col-sm-2 control-label">Slug<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" id="slug" name="slug" class="form-control" required="" value="{{ ($action == 'Add') ? old('slug') : $cms_page->slug}}" readonly="">
								</div>
							</div>

							<div class="form-group">
								<label for="content" class="col-sm-2 control-label">Content<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<textarea name="content" class="summernote" required="" rows="5">{{ ($action == 'Add') ? old('content') : $cms_page->content}}</textarea>
								</div>
							</div>

							<div class="form-group">
			                	<label class="col-sm-2 control-label">Status</label>
			                	<div class="col-sm-4">
									@php $status = ($action == 'Add') ? old('status') : $cms_page->status @endphp
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
								<a href="{{url('admin/cms-pages')}}">
						        	<button type="button" class="btn  cancel btn-fullrounded">
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
    $(function(){

    	// summernote editor
		$('.summernote').summernote(
		{
			height: 300,
			focus: true,
			onpaste: function()
			{
				alert('You have pasted something to the editor');
			},
			toolbar: [
	            [ 'style', [ 'style' ] ],
	            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'clear'] ],
	            [ 'fontname', [ 'fontname' ] ],
	            [ 'fontsize', [ 'fontsize' ] ],
	            [ 'color', [ 'color' ] ],
	            [ 'insert', [ 'link','picture','video'] ],
	            [ 'para', [ 'ol', 'ul', 'paragraph' ] ],
	            [ 'table', [ 'table' ] ],
	            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview' ] ]
	        ]
		});

		$("#title").keyup(function(){
		    var slug = '';
		    var trimmed = $.trim($(this).val());
		    slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
		    replace(/-+/g, '-').
		    replace(/^-|-$/g, '');
		    $("#slug").val(slug.toLowerCase());    
		});

        $('#cms-page-form').validate({
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
    });

</script>
@endsection
