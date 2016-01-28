@extends('app')

@section('content')
    <table class="table">
        <tr>
            <th>Start</th>
            <th>Station</th>
            <th>Size</th>
            <th>Filename</th>
        </tr>
    @foreach($files as $file)
        <tr>
            <td>{{$file->start}}</td>
            <td>{{$file->station}}</td>
            <td> @byteToSize($file->size) </td>
            <td>{{$file->name}}</td>
        </tr>
    @endforeach
    </table>
@stop