@extends('app')

@section('title', 'Suche - ' . $q)

@section('content')

    {!! Form::open(['url'=>'tvprogram/search', 'method'=>'get', 'class' => '']) !!}
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="input-group">
                {!! Form::text('q', $q, ['class'=>'form-control', 'placeholder'=>'Suchen']) !!}
                <span class="input-group-btn">
                    {!! Form::submit('Suchen', ['class'=>'form-control btn btn-primary']) !!}
                </span>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <br>
    <script>
        (function() {
            var cx = '011164098189299933424:5d7r-c5al9o';
            var gcse = document.createElement('script');
            gcse.type = 'text/javascript';
            gcse.async = true;
            gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(gcse, s);
        })();
    </script>
    <div id="cse" style="width: 100%;">
        <gcse:searchresults-only></gcse:searchresults-only>
    </div>
@stop
