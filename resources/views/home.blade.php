@extends('app')

@section('title', 'Der onlinetvrecorder Mirror')

@section('content')
	<div class="row text-center">
		<h1>HQ Mirror</h1>
		@include('partials.ad_728x90')
		<hr>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="bs-example" data-example-id="simple-carousel">
				<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
					<ol class="carousel-indicators">
						@foreach($promotions as $index => $promotion)
						<li data-target="#carousel-example-generic" data-slide-to="{{$index}}" @if($index == 0) class="active" @endif></li>
						@endforeach
					</ol>
					<div class="carousel-inner" role="listbox">
						@foreach($promotions as $index => $promotion)
						<div class="item @if($index == 0) active @endif">
							<a href="{{url($promotion->getLink())}}">
								<img width="945" src="{{asset($promotion->getImageLink())}}">
								<div class="carousel-caption">
									<h3>{{$promotion->title}}</h3>
								</div>
							</a>
						</div>
						@endforeach
					</div>
					<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<hr>
		@include('partials.ad_728x90')
	</div>
	<div class="row">
		<div class="col-md-6">
			@include('partials.tv_programs_list', ['caption' => 'Top Sendungen','items' => $downloads])
		</div>
		<div class="col-md-6">
			@include('partials.tv_programs_list', ['caption' => 'Populär','items' => $views])
		</div>
	</div>
	@include('partials.ad_728x90')
@endsection
