<?php

namespace App\Http\Controllers;

use App\Models\Starterkit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StarterkitBookmarkController extends Controller
{
    public function __invoke(Request $request, Starterkit $starterkit)
    {
        $user = Auth::user();
        $message = '';

        DB::transaction(function () use ($starterkit, $user, &$message) {
            if ($starterkit->bookmarks()->where('user_id', $user->id)->exists()) {
                $starterkit->bookmarks()->detach($user->id);
                $starterkit->decrement('bookmark_count');
                $message = 'Bookmark removed';
            } else {
                $starterkit->bookmarks()->attach($user->id);
                $starterkit->increment('bookmark_count');
                $message = 'Bookmark added';
            }
        });

        $starterkit->refresh();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'bookmark_count' => $starterkit->bookmark_count,
                'is_bookmarked' => $starterkit->is_bookmarked
            ]);
        }

        return back();
    }
}
