<?php

use App\Models\Starterkit;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia;

test('it can show a list of starter kits as a guest', function () {
    Starterkit::factory(10)->create();

    $this->get(route('starterkit.index'))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Starterkit/Index')
                ->has('starterkits.data', 10)
        );
});

test('it can show more when requested', function () {
    Starterkit::factory(15)->create();

    $this->get(route('starterkit.load-more', ['page' => 2]))
        ->assertOk()
        ->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 5)
                ->where('current_page', 2)
                ->etc()
        );
});

test('an authenticated user can view the starterkit creation form', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('starterkit.create'))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Starterkit/Create')
        );
});

test('an authenticated user can create new starterkits', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('starterkit.store'), [
        'url' => 'https://github.com/laravel/laravel',
    ])->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('starterkits', [
        'url' => 'https://github.com/laravel/laravel',
    ]);
});

test('an authenticated user can view the edit form for one of their kits', function () {
    $starterkit = Starterkit::factory()->create();

    $this->actingAs($starterkit->user);

    $this->get(route('starterkit.edit', $starterkit))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Starterkit/Edit')
                ->where('starterkit.id', (string) $starterkit->getKey())
                ->etc()
        );
});

test('an authenticated user can update their starterkit', function () {
    $starterkit = Starterkit::factory()->create();

    $this->actingAs($starterkit->user);

    $this->put(route('starterkit.update', $starterkit), [
        'url' => 'https://github.com/laravel/laravel',
    ])->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('starterkits', [
        'url' => 'https://github.com/laravel/laravel',
    ]);
});

test('an authenticated user can delete the starterkit', function () {
    $starterkit = Starterkit::factory()->create();

    $this->actingAs($starterkit->user);

    $this->delete(route('starterkit.destroy', $starterkit))
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseMissing('starterkits', [
        'id' => $starterkit->id,
    ]);
});

test('a guest cannot view the starterkit creation form', function () {
    $this->get(route('starterkit.create'))
        ->assertRedirect(route('login'));
});

test('a guest cannot create new starterkits', function () {
    $this->post(route('starterkit.store'), [
        'url' => 'https://github.com/laravel/laravel',
    ])->assertRedirect(route('login'));
});

test('a guest cannot view the edit form for a kit', function () {
    $starterkit = Starterkit::factory()->create();

    $this->get(route('starterkit.edit', $starterkit))
        ->assertRedirect(route('login'));
});

test('a guest cannot update a starterkit', function () {
    $starterkit = Starterkit::factory()->create();

    $this->put(route('starterkit.update', $starterkit), [
        'url' => 'https://github.com/laravel/laravel',
    ])->assertRedirect(route('login'));
});

test('a guest cannot delete a starterkit', function () {
    $starterkit = Starterkit::factory()->create();

    $this->delete(route('starterkit.destroy', $starterkit))
        ->assertRedirect(route('login'));
});

test('an authenticated user cannot view the edit form for a kit they do not own', function () {
    $starterkit = Starterkit::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('starterkit.edit', $starterkit))
        ->assertForbidden();
});

test('an authenticated user cannot update a starterkit they do not own', function () {
    $starterkit = Starterkit::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('starterkit.update', $starterkit), [
            'url' => 'https://github.com/laravel/laravel',
        ])
        ->assertForbidden();
});

test('an authenticated user cannot delete a starterkit they do not own', function () {
    $starterkit = Starterkit::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('starterkit.destroy', $starterkit))
        ->assertForbidden();
});

test('an administrator can view the edit form for any kit', function () {
    $adminUser = User::factory([
        'id' => 1,
    ])->create();

    $nonAdminUser = User::factory([
        'id' => 100,
    ])->create();

    $starterkit = Starterkit::factory()->for($nonAdminUser)->create();

    $this->actingAs($adminUser)
        ->get(route('starterkit.edit', $starterkit))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Starterkit/Edit')
                ->where('starterkit.id', (string) $starterkit->getKey())
                ->etc()
        );
});

test('an administrator can update any starterkit', function () {
    $adminUser = User::factory([
        'id' => 1,
    ])->create();

    $nonAdminUser = User::factory([
        'id' => 100,
    ])->create();

    $starterkit = Starterkit::factory()->for($nonAdminUser)->create();

    $this->actingAs($adminUser)
        ->put(route('starterkit.update', $starterkit), [
            'url' => 'https://github.com/laravel/laravel',
        ])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('starterkits', [
        'url' => 'https://github.com/laravel/laravel',
        'id' => $starterkit->id,
    ]);
});

test('an administrator can delete any starterkit', function () {
    $adminUser = User::factory([
        'id' => 1,
    ])->create();

    $nonAdminUser = User::factory([
        'id' => 100,
    ])->create();

    $starterkit = Starterkit::factory()->for($nonAdminUser)->create();

    $this->actingAs($adminUser)
        ->delete(route('starterkit.destroy', $starterkit))
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseMissing('starterkits', [
        'id' => $starterkit->id,
    ]);
});
