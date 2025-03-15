<?php

namespace App\Http\Controllers;

use App\Models\Starterkit;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StarterkitController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * GitHub URL validation pattern
     */
    private string $githubUrlPattern = '/^https?:\/\/(www\.)?github\.com\/.*$/i';

    public function dashboard(): Response
    {
        $userStarterkits = Starterkit::where('user_id', Auth::id())
            ->with('tags')
            ->latest()
            ->get();

        $bookmarks = Auth::user()
            ->bookmarks()
            ->with('tags')
            ->latest()
            ->get();

        return Inertia::render('Dashboard', [
            'starterkits' => $userStarterkits,
            'bookmarks' => $bookmarks,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $allStarterkits = $this->queryStarterkits()
            ->orderBy('bookmark_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(self::PER_PAGE);

        $allTags = Tag::all(['id', 'name']);

        return Inertia::render('Starterkit/Index', [
            'starterkits' => $allStarterkits,
            'tags' => $allTags,
            'auth' => [
                'user' => Auth::user() ?? (object) ['name' => 'Guest'],
            ],
        ]);
    }

    /**
     * Load more starterkits for infinite scrolling
     */
    public function loadMore(Request $request): JsonResponse
    {
        $moreStarterkits = $this->queryStarterkits()
            ->orderBy('bookmark_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: self::PER_PAGE, page: $request->input('page', 1));

        return response()->json($moreStarterkits);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Starterkit/Create', [
            'availableTags' => Tag::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => [
                'required',
                'url',
                'unique:starterkits,url',
                'regex:'.$this->githubUrlPattern,
            ],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
        ], [
            'url.regex' => 'The URL must be a valid GitHub repository link.',
        ]);

        $starterkit = Starterkit::create([
            'url' => $validated['url'],
            'user_id' => Auth::id(),
        ]);

        if (isset($validated['tags'])) {
            $starterkit->tags()->sync($validated['tags']);
        }

        return redirect()->route('dashboard')
            ->with('message', 'Starterkit created successfully!');
    }

    public function edit(Request $request, Starterkit $starterkit): Response
    {
        // Check if user owns this starterkit
        abort_unless(
            $request->user()->can('update', $starterkit),
            403,
            'Unauthorized action.',
        );

        return Inertia::render('Starterkit/Edit', [
            'starterkit' => $starterkit->load('tags'),
            'availableTags' => Tag::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Starterkit $starterkit): RedirectResponse
    {
        // Check if user owns this starterkit
        abort_unless(
            $request->user()->can('update', $starterkit),
            403,
            'Unauthorized action.',
        );

        $validated = $request->validate([
            'url' => [
                'required',
                'url',
                'unique:starterkits,url,'.$starterkit->id,
                'regex:'.$this->githubUrlPattern,
            ],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $starterkit->update(['url' => $validated['url']]);
        $starterkit->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('dashboard')
            ->with('message', 'Starterkit updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Starterkit $starterkit): RedirectResponse
    {
        // Check if user owns this starterkit
        abort_unless(
            $request->user()->can('delete', $starterkit),
            403,
            'Unauthorized action.',
        );

        $starterkit->delete();

        return redirect()->route('dashboard')
            ->with('message', 'Starterkit deleted successfully!');
    }

    private function queryStarterkits(): Builder
    {
        return Starterkit::with('tags')
            ->when(
                array_filter(request()->array('tags')) !== [],
                fn (Builder $kits) => $kits->whereHas(
                    'tags',
                    fn (Builder $tags) => $tags->whereIn('id', request()->array('tags'))
                )
            );
    }
}
