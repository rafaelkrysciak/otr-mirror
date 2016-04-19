<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msvalidate.01" content="C7A1EAA9CAE9E604B1A09E48D3703949" />
	<title>HQ-Mirror: @yield('title')</title>

	@yield('head')

	<!-- link href="{{ asset('/css/app.css') }}" rel="stylesheet" -->
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4/css/bootstrap.css" rel="stylesheet">
	<link href="{{ asset('/css/hqm.css') }}" rel="stylesheet">

	<link href="{{ asset('/js/jquery.carousel/jquery.carousel.css') }}" rel="stylesheet">
	<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/css/select2.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

	<link href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" rel="stylesheet">


	@if(!(Auth::user() && Auth::user()->isAdmin()))
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js"></script>
		<script type="text/javascript">
			//<!--

			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-32685473-1']);
			_gaq.push(['_gat._anonymizeIp']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();

			baseUrl = '';
			//-->
		</script>
	@else
		<script>
			window.csrfToken = '{{csrf_token()}}';
			window.appKey = '{{env('API_KEY')}}';
		</script>
	@endif

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
@if(!Request::has('nomenu'))
	<a href="#top" class="go-top"><i class="glyphicon glyphicon-menu-up"></i></a>
	<nav class="navbar navbar-default" id="top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{url('/')}}">HQ-Mirror</a>
			</div>

			<div class="collapse navbar-collapse" id="navbar-collapse">
				@include('partials.navigation')
			</div>

		</div>
	</nav>
@endif
	<div class="container">
		@if(!Session::has('news_seen') && false)
		<div class="alert alert-info" role="alert">
			<strong>Neu!</strong> Für Premium User: Liblings Filme und Serien an einem Platz. <a href="{{url('/news')}}">Mehr erfahren</a>
		</div>
		@endif
		@include('flash::message')
		@include('partials.errors')
		@yield('content')
		<hr>
		@if(!Request::has('nomenu'))
			<div class="row" id="final-footer">
				<div class="col-sm-12 text-center">
					Copyright &copy; 2015 HQ-Mirror. <a href="{{url('impressum')}}">Impressum</a>
				</div>
			</div>
			<br>
		@endif
	</div>

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/Readmore.js/2.0.5/readmore.min.js"></script>
	<script src="{{ asset('/js/jquery.carousel/jquery.carousel.js') }}"></script>
	<script src="{{ asset('/js/hqm.js') }}"></script>

	@yield('scripts')

	@if(Auth::user() && Auth::user()->isAdmin())
		@include('partials.iframe_modal')
	@endif

</body>
</html>
