<p>Hello {{ $user->name ?? 'there' }},</p>
<p>A new episode is available for <strong>{{ $showName }}</strong>!</p>
<p><strong>Episode:</strong> {{ $episode['title'] ?? $episode['name'] ?? '' }}</p>
@if(isset($episode['link']))
<p><a href="{{ $episode['link'] }}">Listen now</a></p>
@endif
<p>Enjoy listening!</p>
<p>— The Pelevo Team</p> 