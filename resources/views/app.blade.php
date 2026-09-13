<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>{{ config('app.name', 'Koriro Coffee Self-Order') }}</title>

    <!-- PWA Manifest & Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#c97d20">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Koriro Coffee">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <!-- Midtrans Snap JS -->
    <script type="text/javascript"
            src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <!-- Vite React Entry -->
    @viteReactRefresh
    @vite(['resources/react/main.jsx'])
</head>
<body class="bg-[#0f0b09] text-[#f5ede6] antialiased">
    <div id="root"></div>

    <script>
        // Registrasi Service Worker untuk PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(error => {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }
    </script>
</body>
</html>
