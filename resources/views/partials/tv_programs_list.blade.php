<table class="table">
    <caption>{{$caption}}</caption>
    <thead>
    <tr>
        <th>Sender</th>
        <th>Datum</th>
        <th>Titel</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td class="nowrap"><span class="label label-default">{{$item->station}}</span></td>
            <td class="nowrap">
                <i class="glyphicon glyphicon-calendar"></i> {{$item->start->format('Y-m-d')}}
                <i class="glyphicon glyphicon-time"></i> {{$item->start->format('H:i')}}</td>
            <td>
                <a href="{{url('tvprogram/show',['id' => $item->tv_program_id])}}">{{$item->title}}</a>
                @if($item->season && $item->episode)
                    (S{{str_pad($item->season, 2, '0', STR_PAD_LEFT)}}E{{str_pad($item->episode, 2, '0', STR_PAD_LEFT)}})
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
