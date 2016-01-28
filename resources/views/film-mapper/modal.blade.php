<div class="modal fade" id="film-mapper-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Fim Mapper</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => '', 'id' => 'film_mapper_form']) !!}
                {!! Form::hidden('id') !!}

                <div class="form-group disabled">
                    {!! Form::label('org_title', 'Original Title') !!}
                    {!! Form::text('org_title', null, ['class' => 'form-control', 'disabled']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('new_title', 'New Title') !!}
                    {!! Form::text('new_title', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('film_id', 'Film') !!}
                    {!! Form::select('film_id', [], null, ['class' => 'form-control film-select', 'style' => 'width:100%;']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('year', 'Year') !!}
                    {!! Form::text('year', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-inline">
                    <div class="form-group">
                        {!! Form::label('min_length', 'Min Length') !!}
                        {!! Form::text('min_length', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('max_length', 'Max Length') !!}
                        {!! Form::text('max_length', null, ['class' => 'form-control']) !!}
                    </div>
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
                        <input name="overwrite" type="checkbox"> Overwrite existing
                    </label>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-mapper">Save changes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->