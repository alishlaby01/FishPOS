<div class="flex h-screen bg-[#F4F7FE] font-sans overflow-hidden" dir="rtl">

    <!-- 1. القائمة الجانبية (Sidebar) -->
    <aside class="w-64 bg-[#1E1B4B] text-white flex flex-col p-6 rounded-l-[2rem] shadow-2xl z-20">
        <!-- اللوجو -->
        <div class="flex items-center gap-3 mb-12 mt-4 px-2">
            <span class="text-3xl">🐟</span>
            <h1 class="text-2xl font-bold tracking-wider">Fish<span class="text-yellow-400">POS</span></h1>
        </div>

        <!-- روابط القائمة -->
        <nav class="flex flex-col gap-3 flex-1">
            <!-- القسم النشط (الأصفر) -->
            <a href="#" class="bg-[#FFD12F] text-[#1E1B4B] px-4 py-3 rounded-2xl font-bold flex items-center gap-4 transition-transform hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                شاشة الكاشير
            </a>
            
            <a href="#" class="text-slate-400 hover:text-white hover:bg-white/10 px-4 py-3 rounded-2xl font-medium flex items-center gap-4 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                الطلبات السابقة
            </a>

            <a href="#" class="text-slate-400 hover:text-white hover:bg-white/10 px-4 py-3 rounded-2xl font-medium flex items-center gap-4 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                الفواتير والوردية
            </a>

            <a href="#" class="text-slate-400 hover:text-white hover:bg-white/10 px-4 py-3 rounded-2xl font-medium flex items-center gap-4 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                الإعدادات
            </a>
        </nav>
        
        <!-- كارت ترويجي صغير تحت -->
        <div class="bg-[#2D2866] p-4 rounded-2xl mt-auto relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="font-bold text-sm mb-1">دعم فني</h4>
                <p class="text-xs text-slate-400 mb-3">تحتاج مساعدة في النظام؟</p>
                <button class="bg-white text-[#1E1B4B] text-xs font-bold px-3 py-1.5 rounded-lg">تواصل معنا</button>
            </div>
            <div class="absolute -bottom-4 -left-4 text-6xl opacity-20">👋</div>
        </div>
    </aside>

    <!-- 2. منطقة المنتجات (الوسط) -->
    <main class="flex-1 flex flex-col p-8 overflow-y-auto">
        <!-- الهيدر (بحث وترحيب) -->
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-[#1E1B4B]">أهلاً بك، علي 👋</h2>
                <p class="text-slate-500 text-sm mt-1">ماذا سنقدم لعملائنا اليوم؟</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="text" placeholder="ابحث عن وجبة..." class="bg-white border-none rounded-full px-6 py-3 pl-12 shadow-sm w-72 focus:ring-2 focus:ring-[#5B45FF] outline-none">
                    <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </header>

        <!-- بانر طبق اليوم (زي الصورة بالظبط) -->
        <div class="bg-gradient-to-r from-[#00A859] to-[#04D676] rounded-3xl p-8 text-white mb-8 flex justify-between items-center shadow-lg relative overflow-hidden h-48">
            <div class="relative z-10">
                <span class="bg-white/20 px-3 py-1 rounded-lg text-xs font-bold mb-3 inline-block">الأكثر مبيعاً 🌟</span>
                <h2 class="text-4xl font-black mb-2">وجبة التوفير العائلية</h2>
                <p class="text-emerald-50 max-w-sm">تشكيلة مميزة من البلطي والجمبري مع الأرز والسلطات بخصم 15% اليوم فقط.</p>
            </div>
            <!-- ممكن تحط صورة وجبة كبيرة هنا -->
            <div class="text-8xl absolute -left-4 -bottom-4 opacity-50">🦐</div>
        </div>

        <!-- الأقسام (Categories) -->
        <div class="flex items-center gap-4 mb-8 overflow-x-auto pb-2 hide-scrollbar">
            <!-- قسم نشط -->
            <button class="bg-[#5B45FF] text-white min-w-[100px] h-24 rounded-2xl flex flex-col items-center justify-center gap-2 shadow-lg shadow-indigo-200 transition-transform hover:-translate-y-1">
                <span class="text-2xl">🐟</span>
                <span class="font-bold text-sm">أسماك</span>
            </button>
            
            <!-- أقسام عادية -->
            <button class="bg-white text-slate-600 min-w-[100px] h-24 rounded-2xl flex flex-col items-center justify-center gap-2 shadow-sm border border-slate-100 transition-all hover:border-[#5B45FF] hover:-translate-y-1">
                <span class="text-2xl">🍤</span>
                <span class="font-bold text-sm">مقليات</span>
            </button>
            
            <button class="bg-white text-slate-600 min-w-[100px] h-24 rounded-2xl flex flex-col items-center justify-center gap-2 shadow-sm border border-slate-100 transition-all hover:border-[#5B45FF] hover:-translate-y-1">
                <span class="text-2xl">🔥</span>
                <span class="font-bold text-sm">مشويات</span>
            </button>
            
            <button class="bg-white text-slate-600 min-w-[100px] h-24 rounded-2xl flex flex-col items-center justify-center gap-2 shadow-sm border border-slate-100 transition-all hover:border-[#5B45FF] hover:-translate-y-1">
                <span class="text-2xl">🍚</span>
                <span class="font-bold text-sm">وجبات</span>
            </button>
        </div>

        <!-- شبكة المنتجات (Popular Dishes) -->
        <div class="flex justify-between items-end mb-4">
            <h3 class="text-xl font-bold text-[#1E1B4B]">المنتجات المتاحة</h3>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-6">
            <!-- كارت منتج 1 -->
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100 relative group transition-all hover:shadow-xl hover:shadow-indigo-50">
                <div class="h-32 bg-slate-50 rounded-2xl mb-4 flex items-center justify-center text-5xl">
                    🐟 <!-- مكان صورة المنتج -->
                </div>
                <div class="flex text-yellow-400 text-xs mb-2">★★★★★</div>
                <h4 class="font-bold text-[#1E1B4B] mb-1">بلطي مشوي رده</h4>
                <div class="flex justify-between items-center mt-4">
                    <span class="text-lg font-black text-[#5B45FF]">145.00 <span class="text-xs text-slate-400">ج.م</span></span>
                    <!-- زرار الإضافة البنفسجي -->
                    <button class="bg-[#5B45FF] text-white w-8 h-8 rounded-lg flex items-center justify-center hover:bg-indigo-700 transition-colors shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            </div>

            <!-- كارت منتج 2 -->
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100 relative group transition-all hover:shadow-xl hover:shadow-indigo-50">
                <div class="h-32 bg-slate-50 rounded-2xl mb-4 flex items-center justify-center text-5xl">
                    🦐
                </div>
                <div class="flex text-yellow-400 text-xs mb-2">★★★★★</div>
                <h4 class="font-bold text-[#1E1B4B] mb-1">وجبة جمبري جامبو</h4>
                <div class="flex justify-between items-center mt-4">
                    <span class="text-lg font-black text-[#5B45FF]">320.00 <span class="text-xs text-slate-400">ج.م</span></span>
                    <button class="bg-[#5B45FF] text-white w-8 h-8 rounded-lg flex items-center justify-center hover:bg-indigo-700 transition-colors shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            </div>
            
            <!-- كارت منتج 3 -->
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100 relative group transition-all hover:shadow-xl hover:shadow-indigo-50">
                <div class="h-32 bg-slate-50 rounded-2xl mb-4 flex items-center justify-center text-5xl">
                    🥗
                </div>
                <div class="flex text-yellow-400 text-xs mb-2">★★★★☆</div>
                <h4 class="font-bold text-[#1E1B4B] mb-1">سلطة طحينة</h4>
                <div class="flex justify-between items-center mt-4">
                    <span class="text-lg font-black text-[#5B45FF]">15.00 <span class="text-xs text-slate-400">ج.م</span></span>
                    <button class="bg-[#5B45FF] text-white w-8 h-8 rounded-lg flex items-center justify-center hover:bg-indigo-700 transition-colors shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- 3. قائمة الطلب / الفاتورة (يسار) -->
    <aside class="w-96 bg-white border-r border-slate-100 flex flex-col p-6 shadow-[-10px_0_30px_-15px_rgba(0,0,0,0.05)] z-10">
        
        <!-- تفاصيل الطاولة / العميل -->
        <div class="bg-[#F4F7FE] rounded-2xl p-4 mb-6 flex justify-between items-center">
            <div>
                <p class="text-xs text-slate-500 font-bold mb-1">رقم الطلب</p>
                <h3 class="font-black text-[#1E1B4B] text-xl">#1024</h3>
            </div>
            <div class="flex gap-2">
                <button class="bg-[#FFD12F] text-[#1E1B4B] w-10 h-10 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </button>
            </div>
        </div>

        <h3 class="font-bold text-[#1E1B4B] mb-4 text-lg">الطلب الحالي (Order Menu)</h3>

        <!-- عناصر الطلب -->
        <div class="flex-1 overflow-y-auto pr-2 space-y-4 custom-scrollbar">
            
            <!-- عنصر واحد في الفاتورة -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-xl">🐟</div>
                    <div>
                        <h4 class="font-bold text-sm text-[#1E1B4B]">بلطي مشوي رده</h4>
                        <p class="text-xs text-slate-400 mt-1">الكمية: 2</p>
                    </div>
                </div>
                <span class="font-bold text-[#5B45FF]">290.00</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-xl">🥗</div>
                    <div>
                        <h4 class="font-bold text-sm text-[#1E1B4B]">سلطة طحينة</h4>
                        <p class="text-xs text-slate-400 mt-1">الكمية: 3</p>
                    </div>
                </div>
                <span class="font-bold text-[#5B45FF]">45.00</span>
            </div>
            
        </div>

        <!-- الملخص والدفع -->
        <div class="border-t border-slate-100 pt-4 mt-4">
            
            <!-- إدخال كود خصم -->
            <div class="bg-[#F4F7FE] rounded-xl flex items-center p-1 mb-4 border border-slate-200">
                <input type="text" placeholder="كود الخصم" class="bg-transparent border-none text-sm outline-none px-3 flex-1">
                <button class="bg-[#1E1B4B] text-white px-4 py-2 rounded-lg text-sm font-bold">تطبيق</button>
            </div>

            <div class="flex justify-between items-center mb-2">
                <span class="text-slate-500 font-medium">الخدمة (Service)</span>
                <span class="font-bold text-[#1E1B4B]">10.00 ج.م</span>
            </div>
            <div class="flex justify-between items-center mb-6">
                <span class="text-lg font-bold text-[#1E1B4B]">الإجمالي (Total)</span>
                <span class="text-2xl font-black text-[#5B45FF]">345.00 ج.م</span>
            </div>

            <!-- زرار الدفع الرئيسي -->
            <button class="w-full bg-[#5B45FF] hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-200 transition-all active:scale-95 text-lg flex justify-center items-center gap-2">
                تنفيذ وطباعة (Checkout)
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </aside>

</div>

<style>
    /* تصغير الـ Scrollbar عشان الشكل المودرن */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
    }
</style>