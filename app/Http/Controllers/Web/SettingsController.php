<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Volume;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pinnedVolumes = DB::table('user_pinned_volumes')
            ->join('volumes', 'user_pinned_volumes.volume_id', '=', 'volumes.id')
            ->where('user_pinned_volumes.user_id', $user->id)
            ->orderBy('user_pinned_volumes.position')
            ->select('volumes.id', 'volumes.name', 'volumes.cover_image', 'user_pinned_volumes.position')
            ->get();

        return view('settings.index', [
            'user'          => $user,
            'pinnedVolumes' => $pinnedVolumes,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'display_name' => 'nullable|string|max:60',
        ]);

        Auth::user()->update([
            'display_name' => $request->input('display_name') ?: null,
        ]);

        return back()->with('success', 'Profile updated.');
    }

    public function searchVolumes(Request $request)
    {
        $q = $request->query('q', '');

        $volumes = Volume::where('name', 'like', '%' . $q . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'cover_image']);

        return response()->json($volumes);
    }

    public function pinVolume(Request $request)
    {
        $request->validate([
            'volume_id' => 'required|exists:volumes,id',
            'position'  => 'required|integer|min:0|max:3',
        ]);

        $userId = Auth::id();

        DB::table('user_pinned_volumes')->upsert(
            [
                'user_id'    => $userId,
                'volume_id'  => $request->volume_id,
                'position'   => $request->position,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ['user_id', 'position'],           // unique keys
            ['volume_id', 'updated_at']        // columns to update on conflict
        );

        return response()->json(['status' => 'pinned']);
    }

    public function unpinVolume(Request $request)
    {
        $request->validate([
            'position' => 'required|integer|min:0|max:3',
        ]);

        DB::table('user_pinned_volumes')
            ->where('user_id', Auth::id())
            ->where('position', $request->position)
            ->delete();

        return response()->json(['status' => 'unpinned']);
    }
}