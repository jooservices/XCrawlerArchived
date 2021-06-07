[title {{ $movie->dvd_id }}]
[category Adult,JAV]
[tags {{ $tags }}, {{ $idols }}]
[publicize off]
[excerpt]{{ $description }}[/excerpt]
[status draft]
<p>
    <img src="{{ $movie->cover }}" rel="nofollow" alt="{{$title}}"/>
</p>

<quote>{{ $movie->description }}</quote>

<ul>
    <li><strong>Idols:</strong> {{ $idols }}</li>
    <li><strong>Content ID:</strong> {{ $movie->content_id }}</li>
    <li><strong>Downloadable:</strong> {{ $movie->is_downloadable ? 'YES' : '' }}</li>
    <li><strong>Director:</strong> {{ $movie->director }}</li>
    <li><strong>Studio:</strong> {{ $movie->studio }}</li>
    <li><strong>Label:</strong> {{ $movie->label }}</li>
    <li><strong>Channel:</strong> {{ $movie->channel }}</li>
</ul>

@if(!empty($movie->galleries))
    @foreach ($movie->galleries as $image)
        <img src="{{$image}}" alt="{{$title}}"/>
    @endforeach
@endif

@if(!empty($movie->sample))
    @foreach ($movie->sample as $image)
        <img src="{{$image}}" alt="{{$title}}"/>
    @endforeach
@endif

<p>
    @if($onejav)
        <a href="{{ $onejav->url }}" rel="nofollow">Onejav</a>
    @endif
</p>
