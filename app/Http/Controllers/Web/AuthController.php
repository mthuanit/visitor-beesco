<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            $user = Auth::user();
            $intended = session()->pull('url.intended', '/gate');

            if ($user && method_exists($user, 'isFactoryAccount') && !$user->isFactoryAccount()) {
                if ($intended == url('/gate') || $intended == '/gate') {
                    return redirect('/admin/dashboard');
                }
                return redirect($intended);
            }
            return redirect($intended);
        }

        return back()->withErrors([
            'username' => 'Tài khoản hoặc mật khẩu không chính xác.',
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
