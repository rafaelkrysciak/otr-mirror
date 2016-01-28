<div class="form-group">
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title', $title, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('image', 'Image Upload:') !!}
    {!! Form::file('image', null, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('image_url', 'Image URL:') !!}
    {!! Form::text('image_url', null, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('tv_program_id', 'TV Program:') !!}
    {!! Form::select('tv_program_id', $tvprograms, null, ['class' => 'form-control tv-program-select']) !!}
</div>
<div class="form-group">
    {!! Form::label('search', 'Search Term:') !!}
    {!! Form::text('search', $search, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('position', 'Position:') !!}
    {!! Form::text('position', $position, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::checkbox('active', 'on', $active, ['id'=>'active']) !!} {!! Form::label('active', 'Active') !!}
</div>
<div class="form-group">
    {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}
</div>
