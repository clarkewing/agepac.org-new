<meta charset="utf-8" />

@head

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])

@if(config('services.cloudflare-analytics.token'))
    <script
        defer
        src='https://static.cloudflareinsights.com/beacon.min.js'
        data-cf-beacon='{"token": "{{ config('services.cloudflare-analytics.token') }}"}'
    ></script>
@endif

@fluxAppearance
