@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Oops!</strong> Da scheint etwas schief gelaufen zu sein!<br><br>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif