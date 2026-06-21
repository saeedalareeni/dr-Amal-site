@props(['data','locale'])
@php($l=fn($value)=>is_array($value)?($value[$locale]??$value['ar']??reset($value)):$value)
<div class="section-head reveal"><div><span class="kicker">{{ $l($data['kicker'] ?? '') }}</span><h2>{{ $l($data['title'] ?? '') }}</h2></div><p>{{ $l($data['intro'] ?? '') }}</p></div>
