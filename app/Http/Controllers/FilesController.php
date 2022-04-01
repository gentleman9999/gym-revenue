<?php

namespace App\Http\Controllers;

use App\Enums\SecurityGroupEnum;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class FilesController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        if(is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        $page_count = 10;
        $roles = request()->user()->getRoles();
        $security_group = request()->user()->securityGroup();

        if($security_group === SecurityGroupEnum::ADMIN || $security_group === SecurityGroupEnum::ACCOUNT_OWNER) {
            $files = File::with('client')
                ->whereClientId($client_id)
                ->whereUserId(null)
                ->filter($request->only('search', 'trashed'))
                ->paginate($page_count);
        } else {
            $files = File::with('client')
                ->whereClientId($client_id)
                ->whereUserId(null)
                ->where('permissions', 'like', '%'.strtolower(str_replace(' ', '_', $roles[0])).'%')
                ->filter($request->only('search', 'trashed'))
                ->paginate($page_count);
        }

        return Inertia::render('Files/Show', [
            'files' => $files
        ]);
    }

    public function edit($id)
    {
        if (!$id) {
            Alert::error("No file ID provided")->flash();
            return Redirect::route('files');
        }

        return Inertia::render('Files/Edit', [
            'file' => File::find($id),
        ]);
    }

    public function upload(Request $request)
    {
        return Inertia::render('Files/Upload', [
        ]);
    }
}
