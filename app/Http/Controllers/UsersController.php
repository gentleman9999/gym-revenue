<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Users/Show', [
            'users' => User::filter($request->only('search', 'trashed'))
                ->paginate(10),
            'filters' => $request->all('search', 'trashed')
        ]);
    }
}
