<!-- Single button -->
<div class="btn-group btn-group-lg" style="width: 100%;">
    <button type="button" class="btn btn-default btn-lg dropdown-toggle" style="width: 100%;"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="glyphicon glyphicon-search"></i> Internet Suche <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" style="width: 100%;">
        <li>
            <a href="http://www.amazon.de/gp/search?ie=UTF8&camp=1638&creative=6742&index=dvd&linkCode=ur2&tag=hqmi-21&keywords={!! urlencode($tvProgram->title) !!}"
               target="_blank"><i class="zocial amazon"></i> DVD/BluRay</a>
        </li>
        <li>
            <a href="https://www.youtube.com/results?search_query={!! urlencode($tvProgram->title.' trailer') !!}"
               target="_blank"><i class="zocial youtube"></i> Trailer</a>
        </li>
        <li>
            <a href="http://www.imdb.com/find?s=all&q={!! urlencode($tvProgram->title) !!}"
               target="_blank"><i class="glyphicon glyphicon-facetime-video"></i> IMDb</a>
        </li>
        <li>
            <a href="http://www.google.com/images?q={!! urlencode($tvProgram->title) !!}"
               target="_blank"><i class="zocial google"></i> Cover</a>
        </li>
        @if(!$tvProgram->otrkeyFiles->isEmpty() && ($tvProgram->otrkeyFiles->first()->season || $tvProgram->length < 75))
            <li>
                <a href="http://www.fernsehserien.de/suche/{!! urlencode($tvProgram->title) !!}"
                   target="_blank"><i class=""></i> fernsehserien.de</a>
            </li>
        @endif
        @if(!$tvProgram->otrkeyFiles->isEmpty())
            <li>
                <a href="http://cutlist.at/?find_sort=urating&find_ade=desc&find_what=name&find={!! urlencode($tvProgram->otrkeyFiles->first()->getBaseName()) !!}"
                   target="_blank"><i class="glyphicon glyphicon-scissors"></i> cutlist.at</a>
            </li>
        @endif
        @if($tvProgram->otr_epg_id > 0)
            <li>
                <a href="http://www.onlinetvrecorder.com/v2/?go=download&epg_id={!! urlencode($tvProgram->otr_epg_id) !!}"
                   target="_blank"><i class="sosaicon sosaicon-tv"></i> OnlineTvRecorder</a>
            </li>
        @else
            <li>
                <a href="http://www.onlinetvrecorder.com/v2/?go=list&tab=search&title={!! urlencode($tvProgram->title) !!}"
                   target="_blank"><i class="sosaicon sosaicon-tv"></i> OnlineTvRecorder</a>
            </li>
        @endif
    </ul>
</div>
