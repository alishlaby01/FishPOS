<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FishPOS - الكاشير</title>
    
    <!-- تأكد من وجود ملف الـ JS مع الـ CSS داخل المصفوفة -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles
</head>
<body class="bg-[#f0f9ff] antialiased font-sans">
    

    <livewire:pos-screen />
    @livewireScripts
</body>
</html>