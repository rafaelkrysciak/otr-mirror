<table class="table table-files">
    <tr>
        <th>Dateiname</th>
        <th>Größe</th>
        <th>Qualität</th>
        <th></th>
    </tr>
    @foreach($otrkeyFiles->sortByDesc('size') as $file)
        @if($file->isAvailable())
            <tr>
                <td class="vert-align">
                    <table class="fixed-table">
                        <tr>
                            <td title="{{$file->name}}">{{$file->name}}</td>
                        </tr>
                    </table>
                </td>
                <td class="vert-align nowrap">@byteToSize($file->size)</td>
                <td class="vert-align nowrap">
                    @if($file->quality == 'mpg.avi')
                        <i class="glyphicon glyphicon-sd-video"></i> <strong>SD</strong>
                    @elseif($file->quality == 'mpg.HQ.avi')
                        <i class="glyphicon glyphicon-sd-video"></i> <strong>HQ</strong>
                    @elseif($file->quality == 'mpg.HD.avi')
                        <i class="glyphicon glyphicon-hd-video"></i> <strong>HD</strong>
                    @elseif($file->quality == 'mpg.mp4')
                        <i class="glyphicon glyphicon-phone"></i> <strong>mp4</strong>
                    @elseif($file->quality == 'mpg.HD.ac3')
                        <i class="glyphicon glyphicon-sound-dolby"></i> <strong>AC3</strong>
                    @elseif($file->quality == 'mpg.HQ.fra')
                        <i class="glyphicon glyphicon-volume-up"></i> <strong>FRA</strong>
                    @endif
                </td>
                <td class="vert-align nowrap">
                    <a href="{{url('download', ['user' => Auth::user() ? Auth::user()->id:'guest', 'token' => $token[$file->id], 'filename' => $file->name])}}"
                       class="btn btn-primary download" rel="nofollow">
                        <i class="glyphicon glyphicon-download-alt"></i>
                    </a>
                    <a href="#collapse-link-copy-{{$file->id}}" class="btn btn-info download" role="button" data-toggle="collapse" aria-expanded="false"
                       aria-controls="collapse-link-copy-{{$file->id}}" rel="nofollow">
                        <i class="glyphicon glyphicon-link"></i>
                    </a>
                    <a href="#" data-url="{{url('download', ['user' => Auth::user() ? Auth::user()->id:'guest', 'token' => $token[$file->id], 'filename' => $file->name])}}"
                       class="jdownload-link" alt="Download mit jDownloader" title="Download mit jDownloader" rel="nofollow">
                        <img src="{{asset('img/jdownloader-icon.png')}}" alt="Download mit jDownloader" />
                    </a>
                </td>
            </tr>
            <tr class="collapse" id="collapse-link-copy-{{$file->id}}">
                <td colspan="4" class="bg-primary">
                    <span class="loading">Loading ...</span>
                    <span class="message" style="display: none;"></span>
                    <div class="input-group" style="width: 100%; display: none;">
                        <input type="text" class="form-control" id="link-{{$file->id}}">
                        <span class="input-group-btn">
                            <button class="btn btn-default copy-btn" type="button">
                                <i class="glyphicon glyphicon-copy"></i>
                            </button>
                        </span>
                    </div>
                </td>
            </tr>
        @endif
    @endforeach
</table>
<form id="jdownload-form" action="http://127.0.0.1:9666/flash/add" target="hidden-form" method="POST">
    <input type="hidden" name="passwords" value="">
    <input type="hidden" name="source" value="http://hq-mirror.de/">
    <input type="hidden" name="urls" value="" id="jdownload-urls">
</form>
<iframe style="display:none" name="hidden-form"></iframe>

@section('scripts')
@parent
<script>

    $('.copy-btn').click(function(e) {
        var $this = $(this), msg;

        $this.parents('td').find('input').select();
        $('.copy-btn').removeClass('btn-success').removeClass('btn-danger');

        try {
            var successful = document.execCommand('copy');
            msg = successful ? '' : 'Kopieren des Links fehlgeschlagen';
            if(successful) {
                $this.addClass('btn-success');
            } else {
                $this.addClass('btn-danger');
            }
        } catch (err) {
            msg = 'Kopieren des Links fehlgeschlagen';
        }
        if(msg) $this.parents('td').find('.message').html(msg).show();
    });

    @foreach($otrkeyFiles->sortByDesc('size') as $file)
        @if($file->isAvailable())
            $('#collapse-link-copy-{{$file->id}}').on('show.bs.collapse', function(e) {
                var target = $(e.target);
                var fileId = {{$file->id}};

                target.find('.loading').show();
                target.find('.message').html('').hide();
                target.find('.input-group').hide();
                target.find('input').attr('value','');

                $.ajax({
                    url: '{{url('download-link', ['user' => Auth::user() ? Auth::user()->id:'guest', 'token' => $token[$file->id], 'filename' => $file->name])}}'
                }).done(function(data) {
                    var msg = null;
                    if(data.message || data.status != 'OK') {
                        msg = data.message ? data.message : 'Es ist ein Fehler aufgetreten!'
                        target.find('.message').html(msg).show();
                    }

                    if(data.link) {
                        target.find('input').attr('value', data.link);
                        target.find('.input-group').show();
                    }
                }).fail(function() {
                    target.find('.message').html('Es ist ein Fehler aufgetreten!').show();
                }).always(function(e) {
                    target.find('.loading').hide();
                });
            });
        @endif
    @endforeach
</script>
@stop