@extends('app')

@section('content')
    <table class="table">
        <tr>
            <th>Node</th>
            <th>Filename</th>
            <th>Size</th>
            <th>Downloaded</th>
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
                {{ $download['filename'] }}<br>
                <small>{{ $download['url'] }}</small>
            </td>
            <td>
                @byteToSize($download['size'])
            </td>
            <td title="@byteToSize($download['downloaded'])">
                {{$download['progress'] }}%
            </td>
            <td>
                {{ $download['starttime']->format('Y-m-d H:i') }}<br>
                @if($download['endtime'])
                    {{ $download['endtime']->format('Y-m-d H:i') }}
                @else
                    -
                @endif
            </td>
            <td>
                {{ $download['duration'] }}<br/>
                <small>Last update: {{ $download['lastupdate']->format('Y-m-d H:i') }}</small>
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