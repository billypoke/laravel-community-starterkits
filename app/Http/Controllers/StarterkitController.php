<?php
namespace App\Http\Controllers;
use App\Models\Starterkit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StarterkitController extends Controller
{
    public function dashboard()
    {
        $userStarterkits = Starterkit::where('user_id', Auth::id())
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

        $allStarterkits = Starterkit::with('user:id,name')
        ->latest()
        ->paginate($perPage);

        return Inertia::render('Starterkit/Index', [
            'starterkits' => $allStarterkits,
            'auth' => [
                'user' => Auth::user() ?? (object)['name' => 'Guest']
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

        $moreStarterkits = Starterkit::with('user:id,name')
        ->latest()
        ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($moreStarterkits);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Starterkit/Create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
        ]);
        $validated['user_id'] = Auth::id();
        $starterkit = Starterkit::create($validated);
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
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Starterkit $starterkit)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Starterkit $starterkit)
    {
        //
    }
}
