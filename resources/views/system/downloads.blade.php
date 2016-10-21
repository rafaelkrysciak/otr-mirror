@extends('app')

@section('content')
    <table class="table table-condensed">
        <tr>
            <th>Node</th>
            <th>Count</th>
            <th>Active</th>
            <th>Zombi</th>
            <th>Broken</th>
            <th>Success</th>
            <th>Internal</th>
        </tr>
        @foreach($nodeStats as $nodeName => $nodeStat)
            <tr>
                <th>{{$nodeName}}</th>
                <td>{{$nodeStat['count']}}</td>
                <td>{{$nodeStat['active']}}</td>
                <td>{{$nodeStat['zombi']}}</td>
                <td>{{$nodeStat['broken']}}</td>
                <td>{{$nodeStat['success']}}</td>
                <td>{{$nodeStat['internal']}}</td>
            </tr>
        @endforeach
    </table>
    <br>
    <table class="table table-condensed">
        <tr>
            <th>Distro</th>
            <th class="text-center">Count</th>
            <th class="text-center">Active</th>
            <th class="text-center">Zombi</th>
            <th class="text-center">Broken</th>
            <th class="text-center">Success</th>
            <th class="text-center">Rate</th>
        </tr>
        @foreach($distroStats as $distroName => $distroStat)
            <tr>
                <th class="">{{$distroName}}</th>
                <td class="active text-right">{{$distroStat['count']}}</td>
                <td class="info text-right">{{$distroStat['active']}}</td>
                <td class="danger text-right">{{$distroStat['zombi']}}</td>
                <td class="danger text-right">{{$distroStat['broken']}}</td>
                <td class="success text-right">{{$distroStat['success']}}</td>
                <td class="text-right">{{ round(($distroStat['success']+$distroStat['active'])/$distroStat['count']*100,0) }}%</td>
            </tr>
        @endforeach
    </table>
    <br>
    <table class="table">
        <tr>
            <th>Node</th>
            <th>Filename</th>
            <th>Start/Finish</th>
            <th>Duration</th>
            <th>Canceld</th>
        </tr>
    @foreach($downloads as $download)
        <tr>
            <td>
                {{ $download['node']->short_name }}
            </td>
            <td>
                {{ $download['url'] }}
            </td>
            <td>

                {{ $download['starttime']->isToday() ? $download['starttime']->format('H:i') : $download['starttime']->format('Y-m-d H:i') }}<br>
                @if($download['endtime'])
                    {{ $download['endtime']->isToday() ? $download['endtime']->format('H:i') : $download['endtime']->format('Y-m-d H:i') }}
                @else
                    -
                @endif
            </td>
            <td>
                {{ $download['duration'] }}
            </td>
            <td>
                @if($download['break'] == '1')
                    Canceled
                @elseif($download['size'] != $download['downloaded'] && !$download['endtime'])
                    <a
                            href="{{ url('node/abort-download', ['nodeId' => $download['node']->id, 'downloadId' => $download['id']]) }}"
                            class="cancel btn btn-danger"
                            role="button">
                        Cancel
                    </a>
                @endif
            </td>
        </tr>
    @endforeach
    </table>
@stop


@section('scripts')
    @parent
    <script>
        $('a.cancel').click(function(event) {
            event.stopPropagation();
            var link = $(this);
            link.attr('disabled', 'disabled');

            $.ajax({
                url: this.href
            }).done(function(data, textStatus){
                link.removeAttr('disabled');
                if(textStatus != 'success') {
                    alert("Couldn't send abort request ["+textStatus+"]");
                } else if(data.status != 'OK') {
                    alert(data.message || "Couldn't abort download");
                } else {
                    link.parent().html('Canceled');
                }
            });

            return false;
        });
    </script>
@stop