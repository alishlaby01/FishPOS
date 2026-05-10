<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>أسماك أبو ضاحي - تسجيل الدخول</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen flex items-center justify-center font-sans">
    <!-- الخلفية المتحركة -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-20 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
    </div>

    <!-- محتوى الـ Login -->
    <div class="relative z-10 w-full max-w-md px-6">
        <!-- الشعار والعنوان -->
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-6">
                <span class="text-6xl drop-shadow-lg">🐟</span>
                <h1 class="text-4xl font-black text-white tracking-widest">
                    أسماك <span class="text-yellow-400">أبو ضاحي</span>
                </h1>
            </div>
            <p class="text-slate-400 text-sm font-medium">نظام إدارة نقاط البيع الحديث لمطاعم الأسماك</p>
        </div>

        <!-- البطاقة الرئيسية -->
        <div class="bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-slate-700/50 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-8 text-center">تسجيل الدخول</h2>

            <!-- عرض الأخطاء -->
            @if ($errors->any())
                <div class="mb-6 bg-red-500/20 border border-red-500/50 rounded-2xl p-4">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-400 text-sm font-medium mb-1">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- عرض الرسائل -->
            @if (session('success'))
                <div class="mb-6 bg-green-500/20 border border-green-500/50 rounded-2xl p-4">
                    <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <!-- نموذج الدخول -->
            <form method="POST" action="{{ route('auth.login') }}" class="space-y-6">
                @csrf

                <!-- حقل البريد الإلكتروني -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-300 mb-2">البريد الإلكتروني</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder="أدخل بريدك الإلكتروني"
                            class="w-full bg-slate-700/50 border border-slate-600 rounded-xl px-4 pr-12 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-transparent transition-all"
                            required
                        >
                    </div>
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- حقل كلمة المرور -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-300 mb-2">كلمة المرور</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="أدخل كلمة المرور"
                            class="w-full bg-slate-700/50 border border-slate-600 rounded-xl px-4 pr-12 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-transparent transition-all"
                            required
                        >
                    </div>
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- تذكرني -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="w-4 h-4 rounded bg-slate-700 border-slate-600 text-purple-500 focus:ring-2 focus:ring-purple-500/50 cursor-pointer"
                    >
                    <label for="remember" class="ar-text mr-2 text-sm text-slate-400 cursor-pointer hover:text-slate-300 transition-colors">تذكرني في المرة القادمة</label>
                </div>

                <!-- زر الدخول -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white font-bold py-3 rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-purple-500/50 active:scale-95 flex items-center justify-center gap-2 text-lg"
                >
                    <span>تسجيل الدخول</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </form>

            <!-- بيانات التطوير (للاختبار فقط) -->
            <div class="mt-8 pt-6 border-t border-slate-700/50">
                <p class="text-xs text-slate-500 text-center mb-3 font-medium">📝 بيانات التطوير (للاختبار)</p>
                <div class="space-y-2 bg-slate-700/30 rounded-lg p-3">
                    <p class="text-xs text-slate-400"><span class="text-purple-400 font-mono">Email:</span> owner@fishpos.test</p>
                    <p class="text-xs text-slate-400"><span class="text-purple-400 font-mono">Password:</span> password</p>
                </div>
            </div>
        </div>

        <!-- الـ Footer -->
        <div class="mt-8 flex flex-col gap-3 text-center sm:text-left sm:flex-row sm:items-center sm:justify-between text-slate-400/80 text-xs font-medium">
            <div>
                © {{ now()->year }} أسماك أبو ضاحي • جميع الحقوق محفوظة
            </div>
            <div class="inline-flex flex-wrap items-center justify-center gap-2 text-slate-400/70">
                <span>Developed by <span class="text-white font-semibold">ALI SHLABY</span> | SOFTWARE ENGINEER</span>
                <span class="inline-flex items-center gap-1 text-slate-300 hover:text-white transition-colors">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16.5 7.5c-.2-.2-1.4-1.1-1.6-1.2-.2-.1-.4-.2-.6 0-.2.2-.8.9-.9 1.1-.1.2-.2.4 0 .7.1.2.2.4.3.5.1.1.2.2.3.3.1.1.1.2.1.3 0 .1 0 .3-.1.4l-.8 1.4c-.1.2-.2.3-.4.3-.2 0-.4-.1-.7-.2-.2-.1-1.4-.7-2.5-1.6-.9-.7-1.5-1.5-1.5-2.1 0-.5.2-1 .6-1.4.4-.5 1.1-.8 1.7-.8.6 0 1.4.1 2.1.5.7.4 1.4 1 1.9 1.5.5.5.9 1.1 1.1 1.8.2.7.2 1.4.1 2.1 0 .2-.1.4-.1.6-.1.2-.2.4-.2.5-.1.1-.2.2-.3.3-.1.1-.4.1-.6 0-.2 0-1.1-.5-1.5-.8-.5-.2-.8-.5-1-.6-.2-.1-.3-.1-.3-.2 0-.1 0-.2.1-.3.1-.1.2-.2.3-.4.1-.1.2-.2.2-.3 0-.2 0-.5-.1-.7-.1-.2-.5-.9-.6-1.1-.1-.2-.2-.3-.5-.4z" />
                    </svg>
                    <a href="https://wa.me/201000000000" target="_blank" rel="noreferrer" class="hover:text-white transition-colors">Support</a>
                </span>
            </div>
        </div>
        <div class="absolute bottom-4 right-6 text-slate-400/70 text-[11px]">Version 1.0.4</div>
    </div>

    <style>
        .ar-text {
            unicode-bidi: bidi-override;
            direction: rtl;
        }
    </style>
</body>
</html>
