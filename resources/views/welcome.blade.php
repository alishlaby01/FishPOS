<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>أسماك أبو ضاحي - نظام إدارة نقاط البيع</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen flex items-center justify-center font-sans">
    <!-- الخلفية المتحركة -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-20 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
    </div>

    <!-- محتوى الصفحة -->
    <div class="relative z-10 text-center space-y-8">
        <!-- الشعار الرئيسي -->
        <div class="flex justify-center items-center gap-4 mb-12">
            <span class="text-8xl drop-shadow-lg animate-bounce">🐟</span>
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-widest">
                أسماك <span class="text-yellow-400">أبو ضاحي</span>
            </h1>
        </div>

        <!-- العنوان والوصف -->
        <div class="space-y-4">
            <h2 class="text-3xl md:text-4xl font-bold text-white">مرحباً بك في أسماك أبو ضاحي</h2>
            <p class="text-slate-300 text-lg md:text-xl max-w-xl mx-auto leading-relaxed">
                نظام إدارة نقاط بيع حديث لمطاعم الأسماك<br>
                🚀 تحكم كامل • 📊 تقارير فورية • ⚡ سرعة عالية
            </p>
            <p class="text-slate-400 text-sm md:text-base font-semibold">ALI SHLABY SOFTWARE ENGINEER</p>
        </div>

        <!-- الأزرار -->
        <div class="flex flex-col md:flex-row gap-4 justify-center items-center pt-8">
            @auth
                <!-- إذا كان المستخدم مسجل دخول -->
                <a href="{{ route('cashier') }}" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white font-bold rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-purple-500/50 active:scale-95 text-lg">
                    🛒 اذهب إلى الكاشير
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-8 py-4 bg-red-500/20 hover:bg-red-500/30 text-red-400 hover:text-red-300 font-bold rounded-xl transition-all border border-red-500/50 text-lg">
                        تسجيل الخروج
                    </button>
                </form>
            @else
                <!-- إذا لم يكن المستخدم مسجل دخول -->
                <a href="{{ route('login') }}" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white font-bold rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-purple-500/50 active:scale-95 text-lg flex items-center gap-2">
                    🔐 تسجيل الدخول
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            @endauth
        </div>

        <!-- الإحصائيات -->
        @if(!auth()->check())
        <div class="grid grid-cols-3 gap-4 mt-16 text-center max-w-2xl mx-auto">
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl shadow-black/20">
                <p class="text-4xl font-black text-yellow-400 mb-2">⚡</p>
                <p class="text-white font-bold">سريع</p>
                <p class="text-slate-400 text-sm">معالجة فورية</p>
            </div>
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl shadow-black/20">
                <p class="text-4xl font-black text-purple-400 mb-2">📊</p>
                <p class="text-white font-bold">ذكي</p>
                <p class="text-slate-400 text-sm">تقارير دقيقة</p>
            </div>
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl shadow-black/20">
                <p class="text-4xl font-black text-cyan-400 mb-2">🔒</p>
                <p class="text-white font-bold">آمن</p>
                <p class="text-slate-400 text-sm">بيانات محمية</p>
            </div>
        </div>
        @endif
    </div>

    <!-- الـ Footer -->
    <div class="absolute inset-x-0 bottom-6 px-6">
        <div class="mx-auto max-w-3xl flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between text-xs text-slate-400/90">
            <div class="space-y-1 text-center sm:text-left">
                <p>© {{ now()->year }} أسماك أبو ضاحي • جميع الحقوق محفوظة</p>
                <p class="text-slate-400/70">
                    Developed by <span class="text-white font-semibold">ALI SHLABY</span> | SOFTWARE ENGINEER
                    ·
                    <a href="https://wa.me/201000000000" target="_blank" rel="noreferrer" class="inline-flex items-center gap-1 text-slate-300 hover:text-white transition-colors">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16.5 7.5c-.2-.2-1.4-1.1-1.6-1.2-.2-.1-.4-.2-.6 0-.2.2-.8.9-.9 1.1-.1.2-.2.4 0 .7.1.2.2.4.3.5.1.1.2.2.3.3.1.1.1.2.1.3 0 .1 0 .3-.1.4l-.8 1.4c-.1.2-.2.3-.4.3-.2 0-.4-.1-.7-.2-.2-.1-1.4-.7-2.5-1.6-.9-.7-1.5-1.5-1.5-2.1 0-.5.2-1 .6-1.4.4-.5 1.1-.8 1.7-.8.6 0 1.4.1 2.1.5.7.4 1.4 1 1.9 1.5.5.5.9 1.1 1.1 1.8.2.7.2 1.4.1 2.1 0 .2-.1.4-.1.6-.1.2-.2.4-.2.5-.1.1-.2.2-.3.3-.1.1-.4.1-.6 0-.2 0-1.1-.5-1.5-.8-.5-.2-.8-.5-1-.6-.2-.1-.3-.1-.3-.2 0-.1 0-.2.1-.3.1-.1.2-.2.3-.4.1-.1.2-.2.2-.3 0-.2 0-.5-.1-.7-.1-.2-.5-.9-.6-1.1-.1-.2-.2-.3-.5-.4z" />
                        </svg>
                        Support
                    </a>
                </p>
            </div>
            <div class="text-slate-400/70">Version 1.0.4</div>
        </div>
    </div>

    <script>
        // إعادة توجيه تلقائية إذا كان المستخدم مسجل دخول
        if ({{ auth()->check() ? 'true' : 'false' }}) {
            window.location.href = '{{ route("cashier") }}';
        }
    </script>
</body>
</html>
