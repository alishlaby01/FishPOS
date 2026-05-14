<!DOCTYPE html>
<html class="dark" dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELITE SEAFOOD POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <style>
        /* Neumorphic Styles */
        .neumorphic-lift {
            box-shadow: 8px 8px 16px #0f0f0f, -8px -8px 16px #252525;
        }
        .neumorphic-sunken {
            box-shadow: inset 8px 8px 16px #0f0f0f, inset -8px -8px 16px #252525;
        }
        .neumorphic-lift:hover {
            box-shadow: 12px 12px 24px #0f0f0f, -12px -12px 24px #252525;
        }
    </style>
</head>
<body class="bg-surface text-on-surface font-cairo overflow-auto min-h-screen flex flex-col">
    {{ $slot }}

    <!-- Toast Notifications -->
    @include('components.toast')

    @livewireScripts
</body>
</html>