<?php

use App\Models\Starterkit;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('an authenticated user can retrieve a list of their bookmarked starterkits', function () {
    $user = User::factory()->create();

    $starterkits = Starterkit::factory(10)->create();

    $user->bookmarks()->sync($starterkits->pluck('id')->map->toString());

    $this->actingAs($user)
        ->getJson(route('starterkit.bookmarks'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Starterkit/Bookmarks')
            ->has('starterkits', 10)
        );
});

test('a guest cannot retrieve a list of bookmarked starterkits', function () {
    $this->getJson(route('starterkit.bookmarks'))
        ->assertUnauthorized();
});
