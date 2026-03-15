@if(config('analytics.enabled') && config('analytics.gtag.id'))
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.gtag.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('analytics.gtag.id') }}', { 'anonymize_ip': {{ config('analytics.gtag.anonymize_ip') ? 'true' : 'false' }} });
    </script>
@endif

@if(config('analytics.gtm.id'))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('analytics.gtm.id') }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
@endif
