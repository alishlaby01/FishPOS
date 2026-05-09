<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FishPOS - إدارة المصاريف</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="bg-[#f0f9ff] antialiased font-sans">

    <livewire:expense-manager />

    @livewireScripts
</body>
</html>