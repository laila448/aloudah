<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginFormadmin()
    {
        return view('auth.loginadmin');
    }

    public function loginadmin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('admin_web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            return redirect()->intended(route('index'));
        }

        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function logoutadmin(Request $request)
    {
        Auth::guard('admin_web')->logout();
        $request->session()->invalidate();
        return redirect()->route('login');
    }
} 