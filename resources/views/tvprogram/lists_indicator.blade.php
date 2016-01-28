@if(Auth::user())
    <a href="#"
        @if(array_key_exists(\App\User::FAVORITE, $lists) && in_array($tv_program_id, $lists[\App\User::FAVORITE]))
            class="add-to-list list-active"
        @else
            class="add-to-list"
        @endif
        data-list="{{\App\User::FAVORITE}}" data-id="{{$tv_program_id}}" title="Merken" data-toggle="tooltip">
            <i class="glyphicon glyphicon-star"></i>
    </a>
    <a href="#"
        @if(array_key_exists(\App\User::WATCHED, $lists) && in_array($tv_program_id, $lists[\App\User::WATCHED]))
            class="add-to-list list-active"
        @else
            class="add-to-list"
        @endif
        data-list="{{\App\User::WATCHED}}" data-id="{{$tv_program_id}}" title="Gesehen" data-toggle="tooltip">
            <i class="glyphicon glyphicon-ok-circle"></i>
    </a>
@else
    <span class="disabled" style="color: lightgray">
        <i class="glyphicon glyphicon-star"></i> <i class="glyphicon glyphicon-ok-circle"></i>
    </span>
@endif