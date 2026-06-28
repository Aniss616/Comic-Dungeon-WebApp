<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Issue;
use App\Models\UserList;
use App\Models\User;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $year = (int) $request->query('year', now()->year);

        // ── Sidebar counts ──────────────────────────────────────────────
        $readsCount    = $user->reads()->count();
        $favsCount     = $user->favourites()->count();
        $favCharsCount = $user->favouriteCharacters()->count();
        $wishlistCount = $user->wishlist()->count();
        $listsCount    = $user->lists()->count();

        // ── Heatmap ─────────────────────────────────────────────────────
        $dateStart = ($year === now()->year)
            ? now()->subDays(365)->toDateString()
            : "{$year}-01-01";
        $dateEnd = ($year === now()->year)
            ? now()->toDateString()
            : "{$year}-12-31";

        $heatmapRaw = DB::table('user_reads')
            ->where('user_id', $user->id)
            ->whereBetween(DB::raw('DATE(read_date)'), [$dateStart, $dateEnd])
            ->selectRaw('DATE(read_date) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->pluck('cnt', 'day');

        $tooltipRaw = DB::table('user_reads')
            ->join('issues', 'user_reads.issue_id', '=', 'issues.id')
            ->join('volumes', 'issues.volume_id', '=', 'volumes.id')
            ->where('user_reads.user_id', $user->id)
            ->whereBetween(DB::raw('DATE(user_reads.read_date)'), [$dateStart, $dateEnd])
            ->selectRaw('DATE(user_reads.read_date) as day, issues.name as issue_name, volumes.name as vol_name, user_reads.read_date')
            ->orderBy('user_reads.read_date', 'desc')
            ->get()
            ->groupBy('day')
            ->map(fn($rows) => $rows->take(3)->values());

        // ── Activity feed ────────────────────────────────────────────────
        $reads = DB::table('user_reads')
            ->join('issues', 'user_reads.issue_id', '=', 'issues.id')
            ->join('volumes', 'issues.volume_id', '=', 'volumes.id')
            ->where('user_reads.user_id', $user->id)
            ->selectRaw("'read' as type, issues.id as issue_id, issues.name as issue_name, volumes.name as vol_name, user_reads.read_date as event_at");

        $favs = DB::table('user_favourites')
            ->join('issues', 'user_favourites.issue_id', '=', 'issues.id')
            ->join('volumes', 'issues.volume_id', '=', 'volumes.id')
            ->where('user_favourites.user_id', $user->id)
            ->selectRaw("'fav' as type, issues.id as issue_id, issues.name as issue_name, volumes.name as vol_name, user_favourites.favourite_date as event_at");

        $activity = DB::table($reads->union($favs), 'activity')
            ->orderBy('event_at', 'desc')
            ->limit(60)
            ->get();

        $activity->transform(function ($item) {
            $item->event_at = \Carbon\Carbon::parse($item->event_at, 'UTC');
            return $item;
        });

        // ── Stats — characters ───────────────────────────────────────────
        $statCharacters = DB::table('user_reads')
            ->join('issue_characters', 'user_reads.issue_id', '=', 'issue_characters.issue_id')
            ->join('characters', 'issue_characters.character_id', '=', 'characters.id')
            ->where('user_reads.user_id', $user->id)
            ->selectRaw('characters.id, characters.name, characters.image, COUNT(*) as read_count')
            ->groupBy('characters.id', 'characters.name', 'characters.image')
            ->orderByDesc('read_count')
            ->limit(10)
            ->get();

        $favCharIds = $user->favouriteCharacters()->pluck('characters.id')->flip();
        $statCharacters = $statCharacters->map(function ($c) use ($favCharIds) {
            $c->is_fav = $favCharIds->has($c->id);
            return $c;
        });

        // ── Stats — volumes ──────────────────────────────────────────────
        $statVolumes = DB::table('user_reads')
            ->join('issues', 'user_reads.issue_id', '=', 'issues.id')
            ->join('volumes', 'issues.volume_id', '=', 'volumes.id')
            ->where('user_reads.user_id', $user->id)
            ->selectRaw('volumes.id, volumes.name, volumes.cover_image, volumes.count_of_issues, COUNT(*) as read_count')
            ->groupBy('volumes.id', 'volumes.name', 'volumes.cover_image', 'volumes.count_of_issues')
            ->orderByDesc('read_count')
            ->limit(10)
            ->get();

        $favCountByVolume = DB::table('user_favourites')
            ->join('issues', 'user_favourites.issue_id', '=', 'issues.id')
            ->where('user_favourites.user_id', $user->id)
            ->selectRaw('issues.volume_id, COUNT(*) as fav_count')
            ->groupBy('issues.volume_id')
            ->pluck('fav_count', 'volume_id');

        $statVolumes = $statVolumes->map(function ($v) use ($favCountByVolume) {
            $v->is_fav = ($favCountByVolume->get($v->id, 0) >= 5);
            return $v;
        });

        // Pinned volumes
        $pinnedVolumes = DB::table('user_pinned_volumes')
            ->join('volumes', 'user_pinned_volumes.volume_id', '=', 'volumes.id')
            ->where('user_pinned_volumes.user_id', $user->id)
            ->orderBy('user_pinned_volumes.position')
            ->select('volumes.id', 'volumes.name', 'volumes.cover_image', 'user_pinned_volumes.position')
            ->get();

        // Wishlist
        $wishlist = $user->wishlist()
            ->with('volume')
            ->orderByPivot('added_at', 'desc')
            ->get();

        // Lists
        $userLists = $user->lists()
            ->withCount('issues')
            ->with(['issues' => fn($q) => $q->limit(3)])
            ->latest()
            ->get();

        // Available years
        $availableYears = DB::table('user_reads')
            ->where('user_id', $user->id)
            ->selectRaw('YEAR(read_date) as yr')
            ->groupBy('yr')
            ->orderByDesc('yr')
            ->pluck('yr');

        return view('profile.index', [
            'user'           => $user,
            'year'           => $year,
            'readsCount'     => $readsCount,
            'favsCount'      => $favsCount,
            'favCharsCount'  => $favCharsCount,
            'wishlistCount'  => $wishlistCount,
            'listsCount'     => $listsCount,
            'heatmapRaw'     => $heatmapRaw,
            'tooltipRaw'     => $tooltipRaw,
            'activity'       => $activity,
            'statCharacters' => $statCharacters,
            'statVolumes'    => $statVolumes,
            'pinnedVolumes'  => $pinnedVolumes,
            'wishlist'       => $wishlist,
            'userLists'      => $userLists,
            'availableYears' => $availableYears,
        ]);
    }

    public function stats()
    {
        $user = Auth::user();

        $allCharacters = DB::table('user_reads')
            ->join('issue_characters', 'user_reads.issue_id', '=', 'issue_characters.issue_id')
            ->join('characters', 'issue_characters.character_id', '=', 'characters.id')
            ->where('user_reads.user_id', $user->id)
            ->selectRaw('characters.id, characters.name, characters.image, COUNT(*) as read_count')
            ->groupBy('characters.id', 'characters.name', 'characters.image')
            ->orderByDesc('read_count')
            ->get();

        $favCharIds = $user->favouriteCharacters()->pluck('characters.id')->flip();

        $allCharacters = $allCharacters->map(function ($c) use ($favCharIds) {
            $c->is_fav = $favCharIds->has($c->id);
        return $c;
        });

        $allVolumes = DB::table('user_reads')
            ->join('issues', 'user_reads.issue_id', '=', 'issues.id')
            ->join('volumes', 'issues.volume_id', '=', 'volumes.id')
            ->where('user_reads.user_id', $user->id)
            ->selectRaw('volumes.id, volumes.name, volumes.cover_image, volumes.count_of_issues, COUNT(*) as read_count')
            ->groupBy('volumes.id', 'volumes.name', 'volumes.cover_image', 'volumes.count_of_issues')
            ->orderByDesc('read_count')
            ->get();

        $favCountByVolume = DB::table('user_favourites')
            ->join('issues', 'user_favourites.issue_id', '=', 'issues.id')
            ->where('user_favourites.user_id', $user->id)
            ->selectRaw('issues.volume_id, COUNT(*) as fav_count')
            ->groupBy('issues.volume_id')
            ->pluck('fav_count', 'volume_id');

        $allVolumes = $allVolumes->map(function ($v) use ($favCountByVolume) {
            $v->is_fav = ($favCountByVolume->get($v->id, 0) >= 5);
            return $v;
        });

        return view('profile.stats', [
            'user'          => $user,
            'allCharacters' => $allCharacters,
            'allVolumes'    => $allVolumes,
        ]);
    }

    public function showList(UserList $list)
    {
        if ($list->user_id !== Auth::id()) abort(403);
        $list->load(['issues.volume']);
        return view('profile.list', [
            'user' => Auth::user(),
            'list' => $list,
        ]);
    }

    public function addIssueToList(Request $request, UserList $list)
    {
        if ($list->user_id !== Auth::id()) abort(403);
        $request->validate(['issue_id' => 'required|exists:issues,id']);

        $maxOrder = DB::table('user_list_issues')
            ->where('user_list_id', $list->id)
            ->max('sort_order') ?? -1;

        try {
            $list->issues()->attach($request->issue_id, ['sort_order' => $maxOrder + 1]);
            $status = 'added';
        } catch (\Illuminate\Database\QueryException $e) {
            $status = 'exists';
        }

        $issue = Issue::with('volume')->find($request->issue_id);

        return response()->json([
            'status' => $status,
            'issue'  => [
                'id'       => $issue->id,
                'name'     => $issue->name,
                'image'    => $issue->image,
                'vol_name' => $issue->volume->name ?? '',
            ],
        ]);
    }

    public function removeIssueFromList(UserList $list, Issue $issue)
    {
        if ($list->user_id !== Auth::id()) abort(403);
        $list->issues()->detach($issue->id);
        return response()->json(['status' => 'removed']);
    }

    public function searchIssues(Request $request)
    {
        $q = $request->query('q', '');

        $issues = Issue::with('volume')
            ->where('name', 'like', '%' . $q . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'image', 'issue_number', 'volume_id']);

        return response()->json($issues->map(fn($i) => [
            'id'       => $i->id,
            'name'     => $i->name,
            'image'    => $i->image,
            'vol_name' => $i->volume->name ?? '',
        ]));
    }

    public function destroyList(UserList $list)
    {
        if ($list->user_id !== Auth::id()) abort(403);
        $list->delete();
        return response()->json(['status' => 'deleted']);
    }

    public function toggleWishlist(Request $request, Issue $issue)
    {
        $user   = Auth::user();
        $exists = $user->wishlist()->where('issue_id', $issue->id)->exists();

        if ($exists) {
            $user->wishlist()->detach($issue->id);
            $status = 'removed';
        } else {
            $user->wishlist()->attach($issue->id, ['added_at' => now()]);
            $status = 'added';
        }

        return response()->json(['status' => $status]);
    }

    public function storeList(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $list = Auth::user()->lists()->create(['name' => $request->name]);
        return response()->json(['id' => $list->id, 'name' => $list->name]);
    }
}