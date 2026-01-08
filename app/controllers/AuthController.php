<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    // GET /login
    public function create(Request $request)
    {
        if (function_exists('isLoggedIn') && isLoggedIn()) {
            return redirect((function_exists('isAdmin') && isAdmin()) ? '/admin' : '/dashboard');
        }

        return view('auth.login');
    }

    // POST /login
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:255'], // username OR email
            'password' => ['required', 'string', 'max:255'],
        ]);

        $login = $data['username'];
        $password = $data['password'];

        $user = DB::table('users')
            ->where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if (! $user) {
            return back()->withErrors(['username' => 'User not found!'])->withInput();
        }

        if (! password_verify($password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password!'])->withInput();
        }

        $request->session()->put([
            'user_id'   => $user->user_id,   // adjust if your PK is different
            'username'  => $user->username,
            'email'     => $user->email,
            'full_name' => $user->full_name,
            // Normalize role to lowercase to avoid mismatches like 'Admin' vs 'admin'
            'user_type' => strtolower((string) ($user->user_type ?? 'user')),
        ]);

        $request->session()->regenerate();

        return redirect(strtolower((string) ($user->user_type ?? 'user')) === 'admin' ? '/admin' : '/dashboard');
    }

    // POST /logout (and optional GET /logout if you added it)
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // GET /register
    public function registerForm()
    {
        if (function_exists('isLoggedIn') && isLoggedIn()) {
            return redirect((function_exists('isAdmin') && isAdmin()) ? '/admin' : '/dashboard');
        }

        return view('auth.register');
    }

    // POST /register
    public function registerStore(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'password'  => ['required', 'string', 'min:6', 'confirmed'], // needs password_confirmation
        ]);

        $insert = [
            'username'  => $data['username'],
            'email'     => $data['email'],
            'full_name' => $data['full_name'],
            'password'  => Hash::make($data['password']),
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $insert['phone'] = $data['phone'] ?? '';
        }

        if (Schema::hasColumn('users', 'user_type')) {
            $insert['user_type'] = 'user';
        }

        if (Schema::hasColumn('users', 'created_at')) {
            $insert['created_at'] = now();
        }
        if (Schema::hasColumn('users', 'updated_at')) {
            $insert['updated_at'] = now();
        }

        DB::table('users')->insert($insert);

        return redirect()->route('login.form')
            ->with('success', 'Registration successful! You can now login.');
    }
}