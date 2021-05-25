[title {{ $idol->name }}]
[category Adult,JAV]
[tags {{ $idol->name }}]
[publicize off]
[excerpt]{{ $idol->name }}[/excerpt]
[status draft]
<p><img src="{{ $idol->cover }}" rel="nofollow" alt="{{$idol->name}}"/></p>

<ul>
    <li><strong>Birthday</strong> {{$idol->birthday->format('Y-m-d')}}</li>
    <li><strong>Blood</strong> {{$idol->blood_type}}</li>
    <li><strong>City</strong> {{$idol->city}}</li>
</ul>

<blockquote>
    <ul>
        <li><strong>Height</strong> {{$idol->height}}</li>
        <li><strong>Breast</strong> {{$idol->breast}}</li>
        <li><strong>Waist</strong> {{$idol->waits}}</li>
        <li><strong>Hips</strong> {{$idol->hips}}</li>
    </ul>
</blockquote>
