<!doctype html>
<html lang="{{ app()->getLocale() }}" class="fullscreen-bg">
	<head>
		<title>Admin | @yield('title')</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<!-- VENDOR CSS -->
		<link rel="stylesheet" href="{{ asset('admin-assets/vendor/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('admin-assets/vendor/font-awesome/css/font-awesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('admin-assets/vendor/themify-icons/css/themify-icons.css') }}">
		<!-- MAIN CSS -->
		<link rel="stylesheet" href="{{ asset('admin-assets/css/main.css') }}">
		<link rel="stylesheet" href="{{ asset('admin-assets/css/new-main.css') }}">
		<!-- GOOGLE FONTS -->
		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
		<!-- ICONS -->
		<link rel="apple-touch-icon" sizes="32x32" href="{{ asset('images/favicon-96x96.png') }}">
		<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-96x96.png') }}">
	</head>
	<body>
		<!-- WRAPPER -->
		<div id="wrapper" class="login-page-par grey-bg">
			<div class="vertical-align-wrap">
				<div class="vertical-align-middle">
					<div class="auth-box">
						<div class="left">
							@yield('content')
						</div>
						
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
        <!-- END WRAPPER -->
        <script src="{{ asset('admin-assets/vendor/jquery/jquery.min.js') }}"></script> 
        <script src="{{ asset('admin-assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('admin-assets/js/jquery.validate.js') }}"></script>
        <script type="text/javascript">
        	setTimeout(function() {
			    $('.alert').fadeOut('slow');
			}, 5000);
        </script>
        @yield('js')
	</body>
</html>