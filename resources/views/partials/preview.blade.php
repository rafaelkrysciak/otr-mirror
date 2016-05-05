<div class="carousel" style="width:250px;min-height:188px;">
    <div class="previews">
        <div class="preview active"><img src="/img/hqm_preview.jpg" /></div>
        @for($i=0;$i<10;$i++)
            <div class="preview">
                <img src="http://thumbs0.onlinetvrecorder.com/{{$otrkeyFile->start->format('y.m.d')}}/{{$otrkeyFile->getBaseName()}}____{{$i}}.jpg" />
            </div>
        @endfor
    </div>
    <div class='controls'>
        <a class='next' data-action='next' href='#'>›</a>
        <a class='prev' data-action='prev' href='#'>‹</a>
    </div>
</div>
