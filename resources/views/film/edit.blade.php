@extends('app')

@section('content')
    <h1>Film Edit</h1>

    {!! Form::model($film, ['method' => 'PATCH', 'url' => ['film/'.$film->id], 'id' => 'film_form']) !!}
    
    <div class="form-group">
        {!! Form::label('title', 'Title') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('original_title', 'Original Title') !!}
        {!! Form::text('original_title', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('year', 'Year') !!}
        {!! Form::text('year', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('country', 'Country') !!}
        {!! Form::text('country', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('description', 'Description') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('genre', 'Genre') !!}
        {!! Form::text('genre', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('director', 'Director') !!}
        {!! Form::text('director', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        <?php $fskOptions = ['', 'o.Al.' => 'o.Al.', '6' => 'FSK 6', '12' => 'FSK 12', '16' => 'FSK 16', '18' => 'FSK 18']; ?>
        {!! Form::label('fsk', 'FSK') !!}
        {!! Form::select('fsk', $fskOptions, null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('amazon_asin', 'Amazon ASIN') !!}
        <div class="input-group">
            {!! Form::text('amazon_asin', null, ['class' => 'form-control']) !!}
            <div class="input-group-btn">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#amazonDescription">Get Description</button>
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('amazon_link', 'Amazon Link') !!}
        {!! Form::text('amazon_link', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('amazon_image', 'Amazon Image') !!}
        {!! Form::text('amazon_image', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('imdb_id', 'iMDb ID') !!}
        <div class="input-group">
            {!! Form::text('imdb_id', null, ['class' => 'form-control']) !!}
        <span class="input-group-btn">
            <div class="input-group-btn">
                <button type="button" class="fromimdb btn btn-default" id="imdbpop">Populate From Imdb</button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="title">Title</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="original_title">Original Title</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="year">Year</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="country">Country</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="genre">Genre</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="director">Director</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="fsk">FSK</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="imdb_rating">Imdb Rating</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="imdb_votes">Imdb Votes</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="imdb_runtime">Imdb Runtime</a></li>
                    <li><a href="javascript:void(0)" class="fromimdb" data-field="tvseries">TV Series</a></li>
                </ul>
            </div>
        </span>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('imdb_image', 'iMDb Image') !!}
        {!! Form::text('imdb_image', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('imdb_rating', 'iMDb Rating') !!}
        {!! Form::text('imdb_rating', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('imdb_votes', 'iMDb Votes') !!}
        {!! Form::text('imdb_votes', null, ['class' => 'form-control']) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('imdb_runtime', 'Runtime') !!}
        {!! Form::text('imdb_runtime', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('trailer', 'Trailer') !!}
        {!! Form::text('trailer', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('dvdkritik', 'Kritik') !!}
        {!! Form::text('dvdkritik', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        <div class="checkbox">
            <label for="tvseries">
                {!! Form::checkbox('tvseries', '1', false, ['id' => 'tvseries']) !!} TV Series
            </label>
        </div>
    </div>
    <div class="form-group">
        {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
    </div>

    <hr>

    <h3>Actors</h3>
    <table class="table" id="sortable">
        <tr>
            <th>Star</th>
            <th>Role</th>
            <th></th>
        </tr>
        @foreach($filmstars as $key => $filmstar)
        <tr>
            <td>
                {!! Form::hidden('position['.$key.']', $filmstar['position'], ['class' => 'form-control position']) !!}
                {!! Form::text('star['.$key.']', $filmstar['star'], ['class' => 'form-control star', 'placeholder' => 'Star']) !!}
            </td>
            <td>
                {!! Form::text('role['.$key.']', $filmstar['role'], ['class' => 'form-control role', 'placeholder' => 'Role']) !!}
            </td>
            <td>
                <button type="button" class="btn btn-default handle">
                    <i class="glyphicon glyphicon-resize-vertical"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </table>

    <div class="form-group">
        {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
    </div>


    {!! Form::close() !!}

    <!-- Modal -->
    <div class="modal fade" id="amazonDescription" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Amazon Description</h4>
                </div>
                <div class="modal-body" id="amazonDescriptionBody">
                    Loading ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    @parent

    <script>
        $('.fromimdb').click(function () {
            var field = $(this).data('field') || '';
            var request = $.ajax({
                url: '{{ url('/film/imdb-data') }}/' + $('#imdb_id').val(),
                beforeSend: function () {
                    $('#imdbpop').button('loading');
                },
                complete: function () {
                    $('#imdbpop').button('reset');
                },
                error: function (xhr, status, message) {
                    alert('Error: '+(message || 'Can\'t process the request'));
                }
            });

            request.done(function (data) {
                var formdata = {};
                if(field != '') {
                    formdata[field] = data[field];
                } else {
                    formdata = data;
                }
                populate($('#film_form'), formdata);


                if(data.cast && data.cast.length > 0) {
                    for(i = 0; i < Math.min(data.cast.length,20); i++) {
                        var person = data.cast[i];
                        $("input[name='star["+i+"]']").val(person.star);
                        $("input[name='role["+i+"]']").val(person.role);
                    }
                }
            });
        });

        function populate(frm, data) {
            $.each(data, function (key, value) {
                var $ctrl = $('[name=' + key + ']', frm);
                switch ($ctrl.attr("type")) {
                    case "text":
                    case "hidden":
                        $ctrl.val(value);
                        break;
                    case "radio":
                        $ctrl.each(function () {
                            if ($(this).attr('value') == value) {
                                $(this).attr("checked", value);
                            }
                        });
                        break;
                    case "checkbox":
                        $ctrl.each(function () {
                            $(this).prop("checked", value);
                        });
                        break;
                    default:
                        $ctrl.val(value);
                }
            });
        }

        $('#amazonDescription').on('show.bs.modal', function () {
            var asin = $('#amazon_asin').val();
            if(asin == '') return false;

            $('#amazonDescriptionBody').html('<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Loading ...');
            $('#amazonDescriptionBody').load('/film/amazon-description/'+asin, function(response, status, xhr) {
                if ( status == "error" ) {
                    var msg = "Sorry but there was an error: ";
                    alert(msg + xhr.status + " " + xhr.statusText);
                }
            });

            var request = $.ajax({
                url: '{{ url('/film/amazon-data') }}/' + asin,
                error: function (xhr, status, message) {
                    alert('Error: '+(message || 'Can\'t process the request'));
                }
            });

            request.done(function (data) {
                populate($('#film_form'), data);
            });


        });


        $( "#sortable tbody" ).sortable({
            handle: ".handle",
            cancel: "input",
            helper: function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            },
            update: function( event, ui ) {
                $("input.position").each(function(key, element) {
                    $(element).val(key+1)
                });
            }
        }).disableSelection();
    </script>
@stop