<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $r) { return view('admin.profile', ['user' => $r->user()]); }

    public function update(Request $r)
    {
        $u = $r->user();
        $d = $r->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email,' . $u->id,
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $u->name = $d['name']; $u->email = $d['email'];
        if (!empty($d['password'])) $u->password = Hash::make($d['password']);
        $u->save();
        return back()->with('ok', 'Profile updated.');
    }
}
