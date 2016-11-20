{!! Form::open(['method' => 'POST', 'url' => 'contact/send']) !!}
<!-- Boddy Form Input -->
<div class="form-group">
    {!! Form::label('name', 'Name') !!}
    {!! Form::text('name', $values['name'], ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::email('email', $values['email'], ['class' => 'form-control']) !!}
</div>
<!-- Boddy Form Input -->
<div class="form-group">
    {!! Form::label('message', 'Nachricht') !!}
    {!! Form::textarea('message', null, ['class' => 'form-control']) !!}
</div>
@if(!Auth::user())
    <div class="form-group">
        <div class="g-recaptcha" data-sitekey="6LchjwgTAAAAAAZQ8roK4EgiYDef2z4qZKUwiIvM"></div>
    </div>
@endif
{!! Form::submit('Senden', ['class' => 'btn btn-primary']) !!}

{!! Form::close() !!}
