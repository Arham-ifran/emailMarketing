@extends('admin.layouts.app')

@section('title', 'Email Campaign Templates')
@section('sub-title', $action.' Email Campaign Template')


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
			<li><a href="{{url('admin/email-campaign-templates')}}"><i class="fa fa-envelope"></i> Email Campaign Templates</a></li>
			<li>{{$action}}</li>
		</ul>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="alert-info p-2" role="alert" style="padding: 20px;">
					<p>On sending campaign, following keywords with double curly brackets e.g <strong> @{{keyword}} </strong> will be replaced by their values:</p>
					<li><strong>name</strong> : Contact's full name </li>
				</div>
				<br>

				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{$action}} Email Campaign Template</h3>
					</div>
					<div class="panel-body">
						@include('admin.messages')
						<form id="email-templates-form" class="form-horizontal label-left" action="{{url('admin/email-campaign-templates')}}" enctype="multipart/form-data" method="POST">
							@csrf

							<input type="hidden" name="action" value="{{$action}}" />
							<input name="id" type="hidden" value="{{ $email_template->id }}" />

							<label for="subject" class="control-label">Name<span class="text-red">*</span></label>
							<div class="form-group">
								<div class="col-sm-12">
									<input type="text" name="name" maxlength="250" class="form-control" required="" value="{{$email_template->name}}">
								</div>
							</div>

							<label for="content" class="control-label">Design<span class="text-red">*</span></label>
							<input type="hidden" name="old_image" value="{{$email_template->image}}">
							<input type="hidden" name="image" id="image">
							<textarea name="content" id="content" style="display:none;">{{$email_template->content}}</textarea>
							<textarea name="html_content" id="html_content" style="display:none;">{{$email_template->html_content}}</textarea>

							<div id="editor-container" style="height: 80vh;"></div>

							<div class="form-group" style="margin-top: 2em;">
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
							</div>

							<div class="text-right mt-3" style="margin-top: 2em;">
								<a href="{{url('admin/email-campaign-templates')}}">
									<button type="button" class="btn cancel btn-fullrounded">
										<span>Cancel</span>
									</button>
								</a>

								<button type="submit" disabled id="submit_btn" class="btn btn-primary btn-fullrounded">
									<span>Save</span>
								</button>
							</div>
						</form>
					</div>
					<div id="capture">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script src="//editor.unlayer.com/embed.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.3/html2canvas.min.js"></script>
<script>
	$(function() {
		unlayer.init({
			appearance: {
				theme: 'dark'
			},
			id: 'editor-container',
			projectId: 1234,
			displayMode: 'email',
		})

		// load design
		var design = document.getElementById('content').value;
		if (design) {
			unlayer.loadDesign(JSON.parse(design));
			setTimeout(function() {
				document.getElementById('submit_btn').disabled = false;
			}, 5000);
		} else {
			document.getElementById('submit_btn').disabled = false;
		}

		unlayer.addEventListener('design:updated', function(data) {
			// Design is updated by the user
			unlayer.exportHtml(function(data) {
				var json = data.design; // design json
				var html = data.html; // design html
				// Save the json, or html here
				document.getElementById('content').value = JSON.stringify(json);
				document.getElementById('html_content').value = html;
				// for image
				document.getElementById("capture").innerHTML = "";
				document.getElementById('capture').style.display = "block";
				var bodyHtml = "<div id='mine'>" + /<body.*?>([\s\S]*)<\/body>/.exec(html)[1] + "</div>";
				var s = bodyHtml;
				var temp = document.createElement('div');
				temp.innerHTML = s;
				var htmlObject = temp.firstChild;
				document.getElementById('capture').appendChild(htmlObject);
				html2canvas(document.getElementById('mine')).then(canvas => {
					const image = canvas.toDataURL("image/png");
					document.getElementById('image').value = image;
				})
				document.getElementById('capture').style.display = "none";
			})
		})

		unlayer.registerCallback('image', function(file, done) {
			var data = new FormData()
			data.append('file', file.attachments[0])

			fetch('/api/upload-template-image', {
				method: 'POST',
				headers: {
					'Accept': 'application/json'
				},
				body: data
			}).then(response => {
				// Make sure the response was valid
				if (response.status >= 200 && response.status < 300) {
					return response
				} else {
					var error = new Error(response.statusText)
					error.response = response
					throw error
				}
			}).then(response => {
				return response.json()
			}).then(data => {
				// Pass the URL back to Unlayer to mark this upload as completed
				done({
					progress: 100,
					url: data.image_url
				})
			})
		})

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
				if ($(validator.errorList).length == 0) {
					document.getElementById("page-overlay").style.display = "block";
					return true;
				}
			}
		});
	});
</script>
@endsection