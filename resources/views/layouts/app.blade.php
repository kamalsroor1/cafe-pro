<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cafe Pro') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- PWA --}}
    <meta name="theme-color" content="#F59E0B">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Cafe Pro">

    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/svg+xml" href="/icons/favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="/icons/favicon-96x96.png">
    <link rel="icon" type="image/x-icon" href="/icons/favicon.ico">

    {{-- Service Worker --}}
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(() => console.log('SW OK'))
            .catch(err => console.error('SW Error:', err));
        });
    }
    </script>
</head>

<body class="bg-base text-gray-100 font-sans flex h-screen overflow-hidden antialiased">
    @include('components.sidebar')

    <div class="flex flex-col flex-1 overflow-hidden">
        @include('components.navbar')

        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>

    @include('components.toast')
</body>

</html>