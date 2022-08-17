<?php

namespace App\Http\Controllers;

use App\Enums\SecurityGroupEnum;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class FilesController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->client_id;

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        $isSearching = $request->has('search');

        $page_count = 10;
        $roles = request()->user()->getRoles();
        $security_group = request()->user()->securityGroup();

        if ($isSearching) {
            if ($security_group === SecurityGroupEnum::ADMIN || $security_group === SecurityGroupEnum::ACCOUNT_OWNER) {
                $files = File::with('client')
                    ->whereClientId($client_id)
                    ->whereUserId(null)
                    ->whereHidden(false)
                    ->whereEntityType(null)
                    ->filter($request->only('search', 'trashed'))
                    ->sort()
                    ->paginate($page_count)
                    ->appends(request()->except('page'));
            } else {
                $files = File::with('client')
                    ->whereClientId($client_id)
                    ->whereUserId(null)
                    ->whereHidden(false)
                    ->whereEntityType(null)
                    ->where('permissions', 'like', '%'.strtolower(str_replace(' ', '_', $roles[0])).'%')
                    ->filter($request->only('search', 'trashed'))
                    ->sort()
                    ->paginate($page_count)
                    ->appends(request()->except('page'));
            }
        } else {
            if ($security_group === SecurityGroupEnum::ADMIN || $security_group === SecurityGroupEnum::ACCOUNT_OWNER) {
                $files = File::with('client')
                    ->whereClientId($client_id)
                    ->whereUserId(null)
                    ->whereHidden(false)
                    ->whereFolder(null)
                    ->whereEntityType(null)
                    ->filter($request->only('search', 'trashed'))
                    ->sort()
                    ->paginate($page_count)
                    ->appends(request()->except('page'));
            } else {
                $files = File::with('client')
                    ->whereClientId($client_id)
                    ->whereUserId(null)
                    ->whereHidden(false)
                    ->whereFolder(null)
                    ->whereEntityType(null)
                    ->where('permissions', 'like', '%'.strtolower(str_replace(' ', '_', $roles[0])).'%')
                    ->filter($request->only('search', 'trashed'))
                    ->sort()
                    ->paginate($page_count)
                    ->appends(request()->except('page'));
            }
        }


        $folders = Folder::with('files')
            ->get();

        return Inertia::render('Files/Show', [
            'files' => $files,
            'folders' => $folders,
        ]);
    }

    public function edit($id)
    {
        if (! $id) {
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

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $client_id = request()->user()->client_id;

        if (is_null($client_id)) {
            abort(403);
        }
        $roles = request()->user()->getRoles();
        $security_group = request()->user()->securityGroup();

        if ($security_group === SecurityGroupEnum::ADMIN || $security_group === SecurityGroupEnum::ACCOUNT_OWNER) {
            $files = File::with('client')
                ->whereClientId($client_id)
                ->whereUserId(null)
                ->filter($request->only('search', 'trashed'))
                ->get();
        } else {
            $files = File::with('client')
                ->whereClientId($client_id)
                ->whereUserId(null)
                ->where('permissions', 'like', '%'.strtolower(str_replace(' ', '_', $roles[0])).'%')
                ->filter($request->only('search', 'trashed'))
                ->get();
        }

        return $files;
    }
}
