<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()
                ->select(['id', 'name', 'created_at'])
                ->orderBy('id')
                ->paginate(10),
        ]);
    }
}
