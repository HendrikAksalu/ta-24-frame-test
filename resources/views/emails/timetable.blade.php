<x-mail::message>
# Tunniplaan {{ $startDate->format('d.m.Y') }} - {{ $endDate->format('d.m.Y') }}

@foreach ($timetableEvents as $day => $events)
## {{ ucfirst($day) }}

<x-mail::table>
| Aeg | Aine | Õpetaja | Ruum |
|:----|:-----|:--------|:-----|
@foreach ($events as $event)
| {{ $event['timeStart'] }} - {{ $event['timeEnd'] }} | {{ $event['nameEt'] }} | {{ collect($event['teachers'])->pluck('name')->join(', ') }} | {{ collect($event['rooms'])->pluck('roomCode')->join(', ') }} |
@endforeach
</x-mail::table>

@endforeach

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
