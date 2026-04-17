<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return view('Login.Login');
    }
    public function loginPost(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $identifier = $request->username;
        $password = $request->password;

        // Try to find the user by username or employee_id
        $userFound = \App\Models\User::where('username', $identifier)
            ->orWhere('employee_id', $identifier)
            ->first();

        if ($userFound) {
            if ($userFound->role === 'FLE'||$userFound->role === 'sales_team') {
                if ($request->ajax()) {
                    return response()->json(['errors' => ['username' => ['You Are Not Allowed To Login']]], 422);
                }
                return redirect()->back()->with('error', 'You Are Not Allowed To Login');
            }

            if (\Illuminate\Support\Facades\Auth::attempt(['username' => $userFound->username, 'password' => $password])) {
                $role = \Illuminate\Support\Facades\Auth::user()->role;
                $redirect = '/';
                
                if (in_array($role, ['admin', 'TLM', 'SLM', 'FLM'])) {
                    $redirect = route('admin.dashboard');
                } elseif (in_array($role, ['sales_team'])) {
                    $redirect = route('rx.index');
                }

                if ($request->ajax()) {
                    return response()->json(['success' => true, 'redirect_to' => $redirect]);
                }

                return redirect($redirect);
            }
        }

        if ($request->ajax()) {
            return response()->json(['errors' => ['username' => ['Invalid credentials']]], 422);
        }

        return redirect()->back()->with('error', 'Invalid username or password');
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
