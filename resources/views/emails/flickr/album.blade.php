[title {{ $album }}]
[category Flickr]
[tags Flickr, {{ $album }}]
[publicize on]
[excerpt]{{ $album }}[/excerpt]
[status draft]

@foreach ($urls as $url)
    <a href="{{$url}}" target="_blank"><img src="{{$url}}" rel="nofollow" alt="{{$album}}"/></a>
@endforeach
