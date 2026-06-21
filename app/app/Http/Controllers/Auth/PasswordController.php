<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function forgot(): View { return view('admin.auth.forgot'); }
    public function email(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        Password::sendResetLink($request->only('email'));
        return back()->with('status', 'إذا كان البريد مسجلاً فسيصل رابط الاستعادة قريبًا.');
    }
    public function reset(Request $request, string $token): View { return view('admin.auth.reset', ['token' => $token, 'email' => $request->email]); }
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['token' => ['required'], 'email' => ['required', 'email'], 'password' => ['required', 'confirmed', 'min:10']]);
        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60), 'must_change_password' => false])->save();
            event(new PasswordReset($user));
        });
        return $status === Password::PASSWORD_RESET ? redirect()->route('login')->with('status', __($status)) : back()->withErrors(['email' => __($status)]);
    }
    public function change(Request $request): RedirectResponse
    {
        $data = $request->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', 'min:10']]);
        $request->user()->update(['password' => $data['password'], 'must_change_password' => false]);
        return back()->with('status', 'تم تحديث كلمة المرور.');
    }
}
