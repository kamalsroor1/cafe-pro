<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cafe Pro') }} - POS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base text-gray-100 font-sans h-screen overflow-hidden antialiased">
    {{ $slot }}
</body>
</html>
