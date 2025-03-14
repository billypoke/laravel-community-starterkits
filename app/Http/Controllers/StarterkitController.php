<?php
namespace App\Http\Controllers;
use App\Models\Starterkit;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class StarterkitController extends Controller
{
    /**
     * GitHub URL validation pattern
     */
    private $githubUrlPattern = '/^https?:\/\/(www\.)?github\.com\/.*$/i';
    public function dashboard()
    {
        $userStarterkits = Starterkit::where('user_id', Auth::id())
            ->with('tags')
            ->latest()
            ->get();
        return Inertia::render('Dashboard', [
            'starterkits' => $userStarterkits
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = 10;
        $query = Starterkit::with(['user:id,name', 'tags']);

        if ($request->has('tags')) {
            $tags = array_filter($request->tags);
            if (!empty($tags)) {
                foreach ($tags as $tagId) {
                    $query->whereHas('tags', function ($q) use ($tagId) {
                        $q->where('tags.id', $tagId);
                    });
                }
            }
        }

        $allStarterkits = $query->latest()->paginate($perPage);
        $allTags = Tag::all(['id', 'name']);

        return Inertia::render('Starterkit/Index', [
            'starterkits' => $allStarterkits,
            'tags' => $allTags,
            'auth' => [
                'user' => Auth::user() ?? (object) ['name' => 'Guest']
            ]
        ]);
    }
    /**
     * Load more starterkits for infinite scrolling
     */
    public function loadMore(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = Starterkit::with(['user:id,name', 'tags']);

        if ($request->has('tags')) {
            $tags = array_filter($request->tags);
            if (!empty($tags)) {
                foreach ($tags as $tagId) {
                    $query->whereHas('tags', function ($q) use ($tagId) {
                        $q->where('tags.id', $tagId);
                    });
                }
            }
        }

        $moreStarterkits = $query
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($moreStarterkits);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Starterkit/Create', [
            'availableTags' => Tag::all(['id', 'name']),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => [
                'required',
                'url',
                'unique:starterkits,url',
                'regex:' . $this->githubUrlPattern,
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
    /**
     * Display the specified resource.
     */
    public function show(Starterkit $starterkit)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Starterkit $starterkit)
    {
        // Check if user owns this starterkit
        if ($starterkit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Starterkit/Edit', [
            'starterkit' => $starterkit->load('tags'),
            'availableTags' => Tag::all(['id', 'name']),
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Starterkit $starterkit)
    {
        if ($starterkit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'url' => [
                'required',
                'url',
                'unique:starterkits,url,' . $starterkit->id,
                'regex:' . $this->githubUrlPattern,
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
    public function destroy(Starterkit $starterkit)
    {
        // Check if user owns this starterkit
        if ($starterkit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $starterkit->delete();
        return redirect()->route('dashboard')
            ->with('message', 'Starterkit deleted successfully!');
    }
    public function bookmarks()
    {
        $bookmarkedStarterkits = Starterkit::whereHas('bookmarks', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with(['user:id,name', 'tags'])
            ->latest()
            ->get();

        return Inertia::render('Starterkit/Bookmarks', [
            'starterkits' => $bookmarkedStarterkits
        ]);
    }
}
