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
                    <a href="{{url('download', ['user' => Auth::user() ? Auth::user()->id:'guest', 'token' => $token[$file->id], 'filename' => $file->name])}}" class="btn btn-primary download">
                        <i class="glyphicon glyphicon-download-alt"></i>
                    </a>
                    <a href="#" data-url="{{url('download', ['user' => Auth::user() ? Auth::user()->id:'guest', 'token' => $token[$file->id], 'filename' => $file->name])}}"
                       class="jdownload-link" alt="Download mit jDownloader" title="Download mit jDownloader">
                        <img src="{{asset('img/jdownloader-icon.png')}}" alt="Download mit jDownloader" />
                    </a>
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
