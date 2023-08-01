<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){
        return view('admin.auth');
    }

    public function authenticate(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // $credentials = $request->except('_token');
        $credentials = $request->only('email', 'password');
        $credentials["role"] = "admin";

        //Cek apakah akun ada di database users dan role nya = admin ?
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'error' => 'Your Credentials Are Wrong'
        ])->withInput();
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
