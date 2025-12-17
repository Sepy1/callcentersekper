<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email'=>'required|email',
            'password'=>'required' 
        ]);

        if(Auth::attempt($attributes))
        {
            session()->regenerate();
            $role = Auth::user()->role;
            if ($role === 'admin') {
                return redirect('/admin/dashboard')->with(['success'=>'You are logged in as admin.']);
            } elseif ($role === 'officer') {
                return redirect('/officer/dashboard')->with(['success'=>'You are logged in as officer.']);
            } elseif ($role === 'qa') {
                return redirect('/qa/dashboard')->with(['success'=>'You are logged in as QA.']);
            } else {
                return redirect('dashboard')->with(['success'=>'You are logged in.']);
            }
        }
        else{
            return back()->withErrors(['email'=>'Email or password invalid.']);
        }
    }
    
    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }
}
