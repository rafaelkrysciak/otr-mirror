@extends('app')

@section('content')
    <ul>
    @foreach($promotions as $promotion)
        <li>
            @if($promotion->active)
                <i class="glyphicon glyphicon-ok-circle"></i>
            @else
                <i class="glyphicon glyphicon-remove-circle"></i>
            @endif
            {{$promotion->id}} {{$promotion->title}} ({{$promotion->position}})
                <a href="{{url('promotion/'.$promotion->id)}}" data-method="delete" data-confirm="Are you sure?" data-handler="form">delete</a>
            <a href="{{url('promotion/'.$promotion->id.'/edit')}}">edit</a>
            <br>
            <img width="200" src="{{asset($promotion->getImageLink())}}">
        </li>
    @endforeach
        <li><a href="{{url('promotion/create')}}">new</a></li>
    </ul>
@stop