<ul class="nav nav-tabs" role="tablist">
@foreach($episodes->pluck('station')->toBase()->unique() as $station)
    <li role="presentation" class="{{$station == $activeStation ? 'active':''}}"><a href="#{{md5($station)}}" aria-controls="{{md5($station)}}" role="tab" data-toggle="tab">{{$station}}</a></li>
@endforeach
</ul>

<div class="tab-content">
@foreach($episodes->groupBy('station') as $station => $items)
    <div role="tabpanel" class="tab-pane {{$station == $activeStation ? 'active':''}}" id="{{md5($station)}}">
        <table class="table">
            <thead>
            <tr>
                <th>Listen</th>
                <th>Sender</th>
                <th>Datum</th>
                <th>Titel</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>
                        @include('tvprogram.lists_indicator', ['tv_program_id' => $item->tv_program_id, 'lists' => $seriesLists])
                    </td>
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
    </div>
@endforeach
</div>