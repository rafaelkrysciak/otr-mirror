<h1>{{ $title }}</h1>
@foreach($reviews as $review)
    <h3>{{ $review['Source'] }}</h3>
    <p>
        {{ $review['Content'] }}
    </p>
@endforeach