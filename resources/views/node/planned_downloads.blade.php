@extends('app')

@section('content')
    <h2>Planned Downloads</h2>
    <table class="table">
        <caption>Total Size @byteToSize($totalSize)
            in {{$files->total()}} Files</caption>
        <tr>
            <th>Filename</th>
            <th>Size</th>
        </tr>
        @foreach($files as $file)
            <tr>
                <td>{{$file->name}}</td>
                <td class="text-right">@byteToSize($file->distro_size)</td>
            </tr>
        @endforeach
    </table>

    {!! $files->render() !!}
@stop