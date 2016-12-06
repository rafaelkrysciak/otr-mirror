<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msvalidate.01" content="C7A1EAA9CAE9E604B1A09E48D3703949" />
	<title>@yield('title') - HQ-Mirror</title>

	@yield('head')

	<!-- link href="{{ asset('/css/app.css') }}" rel="stylesheet" -->
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4/css/bootstrap.css" rel="stylesheet">
	<link href="{{ asset('/css/hqm.css') }}" rel="stylesheet">

	<link href="{{ asset('/js/jquery.carousel/jquery.carousel.css') }}" rel="stylesheet">
	<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

	<link href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" rel="stylesheet">



	<script>
		@if(Auth::user() && Auth::user()->isAdmin())
			var userStatus = 'admin';
			window.csrfToken = '{{csrf_token()}}';
			window.appKey = '{{env('API_KEY')}}';
		@elseif(Auth::user() && Auth::user()->isPremium())
			var userStatus = 'premium';
		@elseif(Auth::user())
			var userStatus = 'registered';
		@else
			var userStatus = 'guest';
		@endif
	</script>


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
@if(!(Auth::user() && Auth::user()->isAdmin()))
	<!-- Google Tag Manager -->
	<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-NMSQLM"
					  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-NMSQLM');</script>
	<!-- End Google Tag Manager -->
@endif

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
			<strong>Achtung!</strong> Ab 9. Juli werden keine spanischen Sendungen mehr bereit gestellt. <a href="{{url('/news')}}">Mehr erfahren</a>
		</div>
		@endif
		@include('flash::message')
		@include('partials.errors')
		@yield('content')
		<hr>
		@if(!Request::has('nomenu'))
			<div class="row" id="final-footer">
				<div class="col-sm-6 copyright">
					Copyright &copy; 2012-{{date('Y')}} HQ-Mirror - <a href="{{url('impressum')}}">Impressum</a>
				</div>
				<div class="col-sm-6 text-right">
					<a target="_blank" class="twitter btn btn-default" href="https://twitter.com/HQMirror"><span class="zocial twitter"></span></a>
					<a target="_blank" class="facebook btn btn-default" href="https://www.facebook.com/hqmirror"><span class="zocial facebook"></span></a>
					<a target="_blank" class="googleplus btn btn-default" href="https://plus.google.com/+HQ-MirrorDE"><span class="zocial googleplus"></span></a>
				</div>
			</div>
			<br>
		@endif
	</div>

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/Readmore.js/2.0.5/readmore.min.js"></script>
	<script src="{{ asset('/js/jquery.carousel/jquery.carousel.js') }}"></script>
	<script src="{{ asset('/js/hqm.js') }}"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/cookie-bar/1/cookiebar-latest.min.js?tracking=1&thirdparty=1&always=1&top=1&privacyPage=http%3A%2F%2Fwww.hq-mirror.de%2Fimpressum"></script>

	@yield('scripts')

	@if(Auth::user() && Auth::user()->isAdmin())
		@include('partials.iframe_modal')
	@endif

</body>
</html>
