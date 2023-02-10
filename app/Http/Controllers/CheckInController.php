<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Locations\Projections\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckInController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CheckIn/Main/Index');
    }

    public function account(Request $request)
    {
        return Inertia::render('CheckIn/Account/Index', [
            'locations' => Location::select('id', 'name')->get(),
        ]);
    }

    public function club(Request $request)
    {
        return Inertia::render('CheckIn/Club/Index');
    }

    public function result(Request $request)
    {
        return Inertia::render('CheckIn/Result/Index');
    }

    public function login(Request $request)
    {
        return Inertia::render('CheckIn/Login/Index');
    }

    public function register(Request $request)
    {
        return Inertia::render('CheckIn/Register/Index');
    }
}
