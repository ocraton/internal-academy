<x-mail::message>
# Reminder: {{ $workshop->title }}

Ciao {{ $user->name }},

Ti ricordiamo che domani hai un workshop a cui sei iscritto.

**Workshop:** {{ $workshop->title }}
**Data e ora:** {{ $workshop->starts_at->format('d/m/Y \a\l\l\e H:i') }}
**Descrizione:** {{ $workshop->description }}

Ci vediamo domani!

<x-mail::button :url="config('app.url')">
Vai all'academy
</x-mail::button>

Grazie,
{{ config('app.name') }}
</x-mail::message>
