<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function index(): Response
    {
        abort_unless(Auth::user()->is_admin, 403);

        return Inertia::render('Tags/Index', [
            'tags' => Tag::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->is_admin, 403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:tags',
            ],
        ]);

        Tag::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        abort_unless(Auth::user()->is_admin, 403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:tags,name,'.$tag->id,
            ],
        ]);

        $tag->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->back();
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        abort_unless(Auth::user()->is_admin, 403);

        $tag->delete();

        return redirect()->back();
    }
}
