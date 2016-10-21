@extends('app')

@section('content')

    <h1>Frequently Asked Questions</h1>

    <br>

    <h3>Was sind *.otrkey-Dateien?</h3>

    <p>
        Otrkey-dateien sind Fernsehsendungen die über <a target="_blank" href="http://www.onlinetvrecorder.com/?ref=701472">OnlineTvRecorder</a>
        aufgenommen wurden. Diese Dateien sind verschlüsselt und können nur <b>entschlüsselt</b> werden wenn sie
        vorher auf OnlineTvRecorder vorgemerkt wurden. Am besten schaut bitte in das Handbuch des Dienstes rein:
        <a target="_blank" href="http://wiki.onlinetvrecorder.com/index.php/Handbuch">
            http://wiki.onlinetvrecorder.com/index.php/Handbuch
        </a>
    </p>

    <p>
        OnlineTvRecorder-Premium User können sich Sendungen über eine Wishlist
        vormerken lassen. Eine besonde Wishlist ist die GetItAll-Wishlist, die
        es erlaub alles vorzumerken was ausgestrahlt wird. Mehr dazu im Wiki: 
        <a target="_blank" href="http://wiki.onlinetvrecorder.com/index.php/Wishlist#GetItAll-Wishlist">
            http://wiki.onlinetvrecorder.com/index.php/Wishlist#GetItAll-Wishlist
        </a>
    </p>

    <br>

    <h3>Was ist OnlineTvRecorder (OTR)?</h3>

    <p>
        OnlineTvRecorder kann man sich wie einen normalen Videorecorder
        vorstellen. Der Dienst nimmt die vorgemerkten Sendungen auf und man kann
        sie später aus dem Internet runterladen. OnlineTvRecorder bietet
        eine große Auswahl an TV-Sendern die Teilweise auch in HD
        aufgenommen werden.
    </p>

    <br>

    <h3>Wenn ich HQ-Mirror Premium kaufe, habe ich dann auch Premium bei OTR?</h3>

    <p>
        Nein! Das HQ-Mirror Premium ist auch nur bei HQ-Mirror gültig. Der Premium-Status
        bei OTR ist davon unabhängig und muss ggf. separat bezahlt werden.
    </p>

    <br>

    <h3>Ist OnlineTvRecorder / HQ-Mirror legal?</h3>

    <p>
        Ja, da der OnlineTvRecorder technisch gesehen wie ein normaler
        Video-Recorder zu Hause funktioniert. Per Gesetzt ist jedem erlaubt das
        Fernsehprogramm aufzunehmen. Es ist dabei auch nicht eingeschränkt,
        welches technische Gerät man dafür benutzen soll.
    </p>

    <br>

    <h3>In welche Qualität werden die Sendungen angeboten?</h3>

    <p>Die Sendungen werde in folgender Qualiät angeboten:</p>
    <table class="table">
        <tr>
            <th>Bezeichnung</th>
            <th>Dateiendung</th>
            <th>Format</th>
        </tr>
        <tr>
            <td>HD</td>
            <td>mpg.HD.avi</td>
            <td>Die Auflösung beträgt je nach Sender 1280 x 720 Pixel bis 1920 x 1080 Pixel</td>
        </tr>
        <tr>
            <td>AC3</td>
            <td>mpg.HD.ac3</td>
            <td>Hierbei handelt es sich "nur" um die Tonspur der (HD-)Aufnahme</td>
        </tr>
        <tr>
            <td>FRA</td>
            <td>mpg.HQ.fra.mp3</td>
            <td>Hierbei handelt es sich um die französische Tonspur der (HQ-)Aufnahme</td>
        </tr>
        <tr>
            <td>HQ</td>
            <td>mpg.HQ.avi</td>
            <td>Die Auflösung beträgt je nach Sender 720 x 480 Pixel bis 720 x 576 Pixel</td>
        </tr>
        <tr>
            <td>SD</td>
            <td>mpg.avi</td>
            <td>
                Die Auflösung kann je nach Größe der gesendeten Fernsehbilder variieren.<br>
                Bei Aufnahmen im Format 16:9 ist die Auflösung 640 x 360 Pixel
            </td>
        </tr>
        <tr>
            <td>MP4</td>
            <td>mpg.mp4</td>
            <td>
                Dieses Format ist speziell für mobile Endgeräte wie z.B. iPhone/iPod oder Handys/Smartphones.
                Die Auflösung ist geringer, die Dateien somit um einiges kleiner. Sie beträgt 384 x 216 Pixel.
            </td>
        </tr>
    </table>
    <p>
        Genauers gibt es unter:
        <a target="_blank" href="http://wiki.onlinetvrecorder.com/index.php/Aufnahmeformate">
            http://wiki.onlinetvrecorder.com/index.php/Aufnahmeformate
        </a>
    </p>
    <br>

    <h3>Neue Frage Stellen</h3>

    {!! Form::open(['method' => 'POST', 'url' => 'contact/send']) !!}
    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('name', 'Name') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('email', 'Email') !!}
        {!! Form::email('email', null, ['class' => 'form-control']) !!}
    </div>
    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('message', 'Frage') !!}
        {!! Form::textarea('message', null, ['class' => 'form-control']) !!}
    </div>

    {!! Form::submit('Senden', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}

@stop