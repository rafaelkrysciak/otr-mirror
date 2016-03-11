<h3>{{$node->short_name}} <small>{{$node->url}}</small> <i class=""></i></h3>
<dl class="dl-horizontal">
    <dt>Last update</dt>
    <dd>{{$node->updated_at->format('Y-m-d H:i')}}</dd>
    <dt>Busy Worker</dt>
    <dd>{{$node->busy_workers}}</dd>
    <dt>Load</dt>
    <dd>{{$status['loadAverage1']}} {{$status['loadAverage5']}} {{$status['loadAverage15']}}</dd>
    <dt>Free Disk Space</dt>
    <dd>
        @byteToSize($node->free_disk_space)
        out of
        @byteToSize($status['totalDiskspace'])
    </dd>
</dl>
