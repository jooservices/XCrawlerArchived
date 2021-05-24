[title {{ $title }}]
[category Adult,JAV]
[tags {{ $tags }}, {{ $idols }}]
[publicize off]
[excerpt]{{ $description }}[/excerpt]
[status draft]
<p><img src="{{ $cover }}" rel="nofollow"/></p>
<quote>{{ $description }}</quote>
<p>
    <strong>Idols:</strong> {{ $idols }}
</p>

<p>
    @if($onejav)
        <a href="{{ $onejav->url }}" rel="nofollow">Onejav</a>
    @endif
</p>
