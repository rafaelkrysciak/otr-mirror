@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Registrieren</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/register') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">Name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="name" value="{{ old('name') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Adresse</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Passwort</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Passwort wiederholen</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password_confirmation">
							</div>
						</div>
						<div class="form-group" >
							<div class="col-md-6 col-sm-offset-4">
								<div class="g-recaptcha" data-sitekey="6LchjwgTAAAAAAZQ8roK4EgiYDef2z4qZKUwiIvM"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Registrieren
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="text-center">
				<p>Hast du bereits ein Konto? Hier geht es zum <a href="{{url('/auth/login')}}" class="btn btn-primary btn-sm">Login</a></p>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
	@parent
	<script src='https://www.google.com/recaptcha/api.js'></script>
@stop