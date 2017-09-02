
{!! Form::hidden('id') !!}
{!! Form::hidden('org_title') !!}

<div class="form-group disabled">
    {!! Form::label('org_title', 'Original Title') !!}
    {!! Form::text('org_title', null, ['class' => 'form-control', 'disabled']) !!}
</div>

<div class="form-group">
    {!! Form::label('new_title', 'New Title') !!}
    <div class="input-group">
        {!! Form::text('new_title', null, ['class' => 'form-control']) !!}
        <div class="input-group-btn">
            <button type="button" class="btn btn-default copy-title">Copy Title</button>
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('film_id', 'Film') !!}
    {!! Form::select('film_id', $films, null, ['class' => 'form-control film-select', 'style' => 'width:100%;']) !!}
</div>

<div class="form-group">
    {!! Form::label('language', 'Language') !!}
    {!! Form::select('language', $languages, null, ['class' => 'form-control', 'style' => 'width:100%;']) !!}
</div>

<div class="form-group">
    {!! Form::label('year', 'Year') !!}
    {!! Form::text('year', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('min_length', 'Min Length') !!}
    {!! Form::text('min_length', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('max_length', 'Max Length') !!}
    {!! Form::text('max_length', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('channel', 'Channel') !!}
    {!! Form::text('channel', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('director', 'Director') !!}
    {!! Form::text('director', null, ['class' => 'form-control']) !!}
</div>

<div class="checkbox">
    <label>
        {!! Form::checkbox('verified') !!} Verified
    </label>
</div>

<div class="checkbox">
    <label>
        {!! Form::checkbox('overwrite') !!} Overwrite existing
    </label>
</div>


<div class="form-group">
    {!! Form::submit($submitBurronText, ['class' => 'btn btn-primary form-control']) !!}
</div>