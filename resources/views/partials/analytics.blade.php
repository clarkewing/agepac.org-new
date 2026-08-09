@if (config('services.cloudflare-analytics.token'))
    <script
        defer
        src="https://static.cloudflareinsights.com/beacon.min.js"
        data-cf-beacon='{"token": "{{ config('services.cloudflare-analytics.token') }}"}'
    ></script>
@endif
