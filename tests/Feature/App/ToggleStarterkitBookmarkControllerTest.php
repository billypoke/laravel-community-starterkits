<?php

use App\Models\Starterkit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('authenticated users can toggle a starterkit favorite', function () {
    $starterkit = Starterkit::factory()->create();

    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('starterkit.bookmark', $starterkit))
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Bookmark added',
        ]);

    $this->assertDatabaseHas('starterkit_bookmarks', [
        'user_id' => $user->getKey(),
        'starterkit_id' => $starterkit->getKey(),
    ]);

    $this->assertDatabaseHas('starterkits', [
        'id' => $starterkit->getKey(),
        'bookmark_count' => 1,
    ]);
});

test('authenticated users can remove a bookmark from a starterkit', function () {
    $starterkit = Starterkit::factory([
        'bookmark_count' => 1,
    ])->create();

    $user = User::factory()->create();

    DB::table('starterkit_bookmarks')->insert([
        'user_id' => $user->getKey(),
        'starterkit_id' => $starterkit->getKey(),
    ]);

    $this->actingAs($user)
        ->postJson(route('starterkit.bookmark', $starterkit))
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Bookmark removed',
        ]);

    $this->assertDatabaseMissing('starterkit_bookmarks', [
        'user_id' => $user->getKey(),
        'starterkit_id' => $starterkit->getKey(),
    ]);

    $this->assertDatabaseHas('starterkits', [
        'id' => $starterkit->getKey(),
        'bookmark_count' => 0,
    ]);
});

test('a guest cannot bookmark a starterkit', function () {
    $starterkit = Starterkit::factory()->create();

    $this->postJson(route('starterkit.bookmark', $starterkit))
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);

    $this->assertDatabaseMissing('starterkit_bookmarks', [
        'starterkit_id' => $starterkit->getKey(),
    ]);
});
