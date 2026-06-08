<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'doctor')->get();

        return view('users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'doctor') {
            return redirect()
                ->route('users.index')
                ->with('error', 'Chỉ được phép xóa tài khoản bác sĩ.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Đã xóa bác sĩ thành công.');
    }
}
