@component('mail::message')
# Ubiquity Availability Update

{{ $name }} is now available.

@component('mail::button', ['url' => $url])
Order now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
