@extends('admin.layouts.app')

@section('title', 'Language Translations')
@section('sub-title', 'Add Language Translation')
@section('content')
<div class="main-content">
	<div class="content-heading clearfix">
		
		<ul class="breadcrumb">
			<li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="{{url('admin/language-translations')}}"><i class="fa fa-language"></i> Language Translations</a></li>
			<li>Add</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">Add Language Translation</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="language-translations-form" class="form-horizontal label-left" action="{{url('admin/language-translations/partial-translate')}}" enctype="multipart/form-data" method="POST">
							@csrf

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
										<option value="all">All Languages</option>
										@foreach($languages as $lang)
										<option value="{{$lang->id}}">{{$lang->name}}</option>
										@endforeach
									</select>
			                	</div>
			              	</div>

							<div class="form-group">
								<label for="name" class="col-sm-2 control-label">Item Id</label>
								<div class="col-sm-10">
									<input type="text" name="item_id" class="form-control" required="">
								</div>
							</div>

							<div class="form-group">
								<label for="name" class="col-sm-2 control-label">Column Name</label>
								<div class="col-sm-10">
									<input type="text" name="column_name" class="form-control" required="">
								</div>
							</div>

							<div class="form-group">
								<label for="text" class="col-sm-2 control-label">Text</label>
								<div class="col-sm-10">
									<textarea name="text" class="form-control" required="" rows="5"></textarea>
								</div>
							</div>

							<div class="text-right">
								<a href="{{url('admin/language-translations')}}">
						        	<button type="button" class="btn cancel btn-fullrounded">
						          		<span>Cancel</span>
						        	</button>
						      	</a>

								<button type="submit" class="btn btn-primary btn-fullrounded">
									<span>Translate</span>
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
