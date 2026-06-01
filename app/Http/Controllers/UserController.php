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
}