<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
  /**
   * Display the login view.
   */
  public function create(): View
  {
    // return view('auth.login');
    $pageConfigs = ['layout' => 'blank']; // Pakai layout blank untuk halaman login
    return view('auth.login', compact('pageConfigs'));
  }

  /**
   * Handle an incoming authentication request.
   */
  // public function store(LoginRequest $request): RedirectResponse
  // {
  //     $request->authenticate();

  //     $request->session()->regenerate();

  //     return redirect()->intended(route('dashboard', absolute: false));
  // }


  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
      $request->session()->regenerate();

      // Cek role dan redirect sesuai
      $user = Auth::user();
      if ($user->hasRole('admin')) {
        return redirect()->route('dashboard.index');
      } elseif ($user->hasRole('perawat')) {
        return redirect()->route('dashboard.index');
      } elseif ($user->hasRole('user')) {
        return redirect()->route('dashboard.index');
      }

      // Default fallback
      return redirect('/');
    }

    return back()->withErrors([
      'email' => 'Login gagal.',
    ]);
  }


  /**
   * Destroy an authenticated session.
   */
  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
  }
}
