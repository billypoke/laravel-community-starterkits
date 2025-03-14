<?php

namespace App\Http\Controllers;

use App\Models\Starterkit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StarterkitBookmarkController extends Controller
{
    public function __invoke(Request $request, Starterkit $starterkit)
    {
        $user = Auth::user();

        if ($starterkit->bookmarks()->where('user_id', $user->id)->exists()) {
            $starterkit->bookmarks()->detach($user->id);
            $message = 'Bookmark removed';
        } else {
            $starterkit->bookmarks()->attach($user->id);
            $message = 'Bookmark added';
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back();
    }
}
