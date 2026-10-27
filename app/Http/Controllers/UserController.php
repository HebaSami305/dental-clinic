<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:users')];
    }

    public function index()
    {
        return view('users.index', ['users' => User::orderBy('id')->get()]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(config('permissions.roles')))],
            'password' => ['required', 'string', 'min:8'],
        ], [], $this->attributes());

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(config('permissions.roles')))],
            'password' => ['nullable', 'string', 'min:8'],
        ], [], $this->attributes());

        // ما بنسمح للمدير يغيّر دور نفسه (عشان ما يقفل على حاله)
        if ($user->id === Auth::id()) {
            $data['role'] = $user->role;
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'ما بتقدر تحذف حسابك']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم');
    }

    private function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'role' => 'الدور',
            'password' => 'كلمة المرور',
        ];
    }
}
