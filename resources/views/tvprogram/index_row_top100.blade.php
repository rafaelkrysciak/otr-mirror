<tr>
    <td class="nowrap text-right">
        <strong>{{$pos}}</strong> 
    </td>
    <td class="nowrap hidden-xs">
        @include('tvprogram.lists_indicator', ['tv_program_id' => $item->tv_program_id, 'lists' => $lists])
    </td>
    <td class="nowrap"><span class="label label-default">{{$item->station}}</span></td>
    <td class="nowrap hidden-xs">
        <i class="glyphicon glyphicon-calendar"></i> {{$item->start->format('Y-m-d')}}
        <i class="glyphicon glyphicon-time"></i> {{$item->start->format('H:i')}}
    </td>
    <td class="nowrap">
        <strong>
            <a href="{{url('tvprogram/show',['id' => $item->tv_program_id])}}">{{$item->title}}</a>
        </strong>
        @if($item->season && $item->episode)
            (S{{str_pad($item->season, 2, '0', STR_PAD_LEFT)}}E{{str_pad($item->episode, 2, '0', STR_PAD_LEFT)}})
        @endif
    </td>
    <td class="visible-md-block visible-lg-block" title="{{$item->description}}" data-toggle="tooltip"
        data-placement="left">
        <table class="fixed-table">
            <tr>
                <td>{{$item->description}}</td>
            </tr>
        </table>
    </td>
</tr>
