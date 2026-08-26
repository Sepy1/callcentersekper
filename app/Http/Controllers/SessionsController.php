<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;


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
            try {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'ticket_id' => null,
                    'action' => 'login',
                    'detail' => 'User logged in',
                    'ip' => request()->ip(),
                ]);
            } catch (\Throwable $e) {
                // don't block login on logging failure
                \Log::error('ActivityLog create failed on login: ' . $e->getMessage());
            }
            $role = Auth::user()->role;
            if ($role === 'admin') {
                return redirect('/admin/dashboard-admin')->with(['success'=>'You are logged in as admin.']);
            } elseif ($role === 'officer') {
                return redirect('/officer/dashboard-officer')->with(['success'=>'You are logged in as officer.']);
            } elseif ($role === 'qa') {
                return redirect('/qa/dashboard-qa')->with(['success'=>'You are logged in as QA.']);
            } elseif ($role === 'cabang') {
                return redirect()->route('cabang.dashboard');
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

        $user = Auth::user();
        try {
            if ($user) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => null,
                    'action' => 'logout',
                    'detail' => 'User logged out',
                    'ip' => request()->ip(),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('ActivityLog create failed on logout: ' . $e->getMessage());
        }

        Auth::logout();

        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }
}
