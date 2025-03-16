<?php

namespace App\Http\Controllers;

use App\Models\Starterkit;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IndexBookmarkedStarterKitsController extends Controller
{
    public function __invoke(): Response
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
