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
            'users' => User::whereHas('detail', function ($query) use ($request) {
                return $query->whereName('associated_client')->whereValue($request->user()->currentClientId());
            })
                ->filter($request->only('search', 'club', 'team'))
                ->paginate(10),
            'filters' => $request->all('search', 'club', 'team')
        ]);
    }
}
