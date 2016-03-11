@extends('app')

@section('content')
	<h1>Node Status</h1>
    @foreach($nodes as $node)
        <div data-nodeid="{{$node->id}}" class="node-info">
        @include('system.nodes_status_partial', [
            'node' => $node,
            'status' => ['loadAverage1' => 'N/A','loadAverage5' => 'N/A','loadAverage15' => 'N/A', 'totalDiskspace' => 'N/A'] ])
        </div>
    @endforeach
@stop


@section('scripts')
    @parent
    <script>
    $('.node-info').each(function(index) {
        $this = $(this);
        var nodeid = $this.data('nodeid');
        $(this).load('{{url('node/status-partial/')}}/'+nodeid);
    });
    </script>
@stop