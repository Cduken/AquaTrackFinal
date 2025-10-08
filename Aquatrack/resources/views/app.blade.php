<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead


    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/icons/icon-192x192.png">

    <!-- For Chrome, Firefox OS, Opera, and Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <!-- For Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes"> <!-- Deprecated, but still useful for older iOS -->
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Clarin Water">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

</head>

<body class="font-sans antialiased">
    @inertia


    <script>
        // Register service worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
</body>

</html>
