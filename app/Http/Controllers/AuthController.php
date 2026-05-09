<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (!in_array($user->role, ['owner', 'cashier'])) {
                Auth::logout();
                return redirect()->back()->withErrors(['role' => 'غير مصرح لك بالدخول']);
            }

            // توجيه حسب الدور
            if ($user->role === 'owner') {
                return redirect()->intended('/owner-dashboard')->with('success', 'مرحباً بك يا صاحب المطعم! 👨‍🍳');
            } else {
                return redirect()->intended('/cashier-dashboard')->with('success', 'مرحباً بك يا كاشير! 👋');
            }
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['credentials' => 'بيانات الدخول غير صحيحة']);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
