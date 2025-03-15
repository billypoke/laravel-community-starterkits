<?php

namespace App\Http\Controllers;

use App\Models\Starterkit;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IndexBookmarkedStarterKitsController extends Controller
{
    public function __invoke()
    {
        $bookmarkedStarterkits = Starterkit::query()
            ->whereRelation('bookmarks', 'user_id', Auth::id())
            ->with('tags')
            ->latest()
            ->get();

        return Inertia::render('Starterkit/Bookmarks', [
            'starterkits' => $bookmarkedStarterkits,
        ]);
    }
}
