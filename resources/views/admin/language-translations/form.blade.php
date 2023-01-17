@extends('admin.layouts.app')

@section('title', 'Language Translations')
@section('sub-title', $action.' Language Translation')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">
		
		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/language-translations')}}"><i class="fa fa-language"></i> Language Translations</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Language Translation</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="language-translations-form" class="form-horizontal label-left" action="{{url('admin/language-translations')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
              				<input name="id" type="hidden" value="{{ $model->id }}"/>

              				@if($action == 'Add')
								<div class="form-group">
									<label for="role" class="col-sm-2 control-label">Language Modules*</label>
									<div class="col-sm-10">
										<select class="form-control" name="language_module_id" required="">
											<option value="">Select Module</option>
	                                        @foreach ($language_modules as $language_module)
	                                            <option value="{{$language_module->id}}">
	                                            	{{$language_module->name}}
	                                            </option>
	                                        @endforeach
	                                    </select>
									</div>
								</div>
								<div class="form-group">
				                	<label class="col-sm-2 control-label">Languages</label>
				                	<div class="col-sm-10">
										<select class="form-control" name="translate_language">
											<option value="">All Lanugages</option>
											@foreach($languages as $lang)
											<option value="{{$lang->id}}">{{$lang->name}}</option>
											@endforeach
										</select>
				                	</div>
				              	</div>

								<div class="form-group">
				                	<label class="col-sm-2 control-label">Translation</label>
				                	<div class="col-sm-10">
										@php $translation_flag = 1; @endphp
										<label class="fancy-radio">
											<input name="translation_flag" value="1" type="radio" {{ ($translation_flag == 1) ? 'checked' : '' }}>
											<span><i></i>Apply On All Records</span>
										</label>
					                  	<label class="fancy-radio">
					                    	<input name="translation_flag" value="2" type="radio" {{ ($translation_flag == 2) ? 'checked' : '' }}>
					                    	<span><i></i>Skip Custom Translated Records</span>
					                  	</label>
				                	</div>
				              	</div>

							@else
								<div class="form-group">
									<label for="name" class="col-sm-2 control-label">Language Module</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" value="{{ $model->languageModule->name}}" readonly="">
									</div>
								</div>

								<div class="form-group">
									<label for="name" class="col-sm-2 control-label">Language Name</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" value="{{ $model->language->name}}" readonly="">
									</div>
								</div>

								<div class="form-group">
									<label for="name" class="col-sm-2 control-label">Language Code</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" value="{{ $model->language->code}}" readonly="">
									</div>
								</div>

								<div class="form-group">
									<label for="name" class="col-sm-2 control-label">Item Id</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" value="{{ $model->item_id}}" readonly="">
									</div>
								</div>

								<div class="form-group">
									<label for="name" class="col-sm-2 control-label">Column Name</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" value="{{ $model->column_name}}" readonly="">
									</div>
								</div>

								@if($model->editor)
									<div class="form-group">
										<label for="item_value" class="col-sm-2 control-label">Item Value</label>
										<div class="col-sm-10">
											<textarea name="item_value" class="summernote" required="" rows="15">{{$model->item_value}}</textarea>
											<div id="content-error" class="help-block" style="display:none"></div>
										</div>
									</div>
								@else
									<div class="form-group">
										<label for="item_value" class="col-sm-2 control-label">Item Value</label>
										<div class="col-sm-10">
											<textarea name="item_value" class="form-control" required="" rows="5">{{ $model->item_value }}</textarea>
										</div>
									</div>
								@endif

								<div class="form-group">
				                	<label class="col-sm-2 control-label">Custom Translation</label>
				                	<div class="col-sm-10">
										@php $custom = ($action == 'Add') ? old('custom') : $model->custom @endphp
										<label class="fancy-radio">
											<input name="custom" value="1" type="radio" {{ ($custom == 1) ? 'checked' : '' }}>
											<span><i></i>Yes</span>
										</label>
					                  	<label class="fancy-radio">
					                    	<input name="custom" value="0" type="radio" {{ ($custom == 0) ? 'checked' : '' }}>
					                    	<span><i></i>No</span>
					                  	</label>
				                	</div>
				              	</div>

				              	<div class="form-group">
				                	<label class="col-sm-2 control-label">WYSIWYG Summernote Editor</label>
				                	<div class="col-sm-10">
										@php $editor = $model->editor @endphp
										<label class="fancy-radio">
											<input name="editor" value="1" type="radio" {{ ($editor == 1) ? 'checked' : '' }}>
											<span><i></i>Enable</span>
										</label>
					                  	<label class="fancy-radio">
					                    	<input name="editor" value="0" type="radio" {{ ($editor == 0) ? 'checked' : '' }}>
					                    	<span><i></i>Disable</span>
					                  	</label>
				                	</div>
				              	</div>
              				@endif

							<div class="text-right">
								<a href="{{url('admin/language-translations')}}">
						        	<button type="button" class="btn cancel btn-fullrounded">
						          		<span>Cancel</span>
						        	</button>
						      	</a>

								<button type="submit" class="btn btn-primary btn-fullrounded">
									<span>{{ ($action == 'Add') ? 'Translate' : 'Save'}}</span>
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
			height: 500,
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

        $('#language-translations-form').validate({
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
