<?php

use App\Models\Tag;
use App\Models\User;

test('a guest cannot index tags', function () {
    $this->get(route('tags.index'))->assertRedirect(route('login'));
});

test('a guest cannot store tags', function () {
    $this->post(route('tags.store'))->assertRedirect(route('login'));
});

test('a guest cannot update tags', function () {
    $tag = Tag::factory()->create();

    $this->put(route('tags.update', $tag))->assertRedirect(route('login'));
});

test('a guest cannot destroy tags', function () {
    $tag = Tag::factory()->create();

    $this->delete(route('tags.destroy', $tag))->assertRedirect(route('login'));
});

test('a non-admin user cannot index tags', function () {
    $this->actingAs(User::factory([
        'id' => 2,
    ])->create());

    $this->get(route('tags.index'))->assertForbidden();
});

test('a non-admin user cannot store tags', function () {
    $this->actingAs(User::factory([
        'id' => 2,
    ])->create());

    $this->post(route('tags.store'))->assertForbidden();
});

test('a non-admin user cannot update tags', function () {
    $this->actingAs(User::factory([
        'id' => 2,
    ])->create());

    $tag = Tag::factory()->create();

    $this->put(route('tags.update', $tag))->assertForbidden();
});

test('a non-admin user cannot destroy tags', function () {
    $this->actingAs(User::factory([
        'id' => 2,
    ])->create());

    $tag = Tag::factory()->create();

    $this->delete(route('tags.destroy', $tag))->assertForbidden();
});

test('an admin user can index tags', function () {
    $this->actingAs(User::factory([
        'id' => 1,
    ])->create());

    $this->get(route('tags.index'))->assertOk();
});

test('an admin user can store tags', function () {
    $this->actingAs(User::factory([
        'id' => 1,
    ])->create());

    $tag = Tag::factory()->make();

    $this->post(route('tags.store'), [
        'name' => $tag->name,
    ])->assertRedirect();

    $this->assertDatabaseHas('tags', [
        'name' => $tag->name,
        'slug' => $tag->slug,
    ]);
});

test('an admin user can update tags', function () {
    $this->actingAs(User::factory([
        'id' => 1,
    ])->create());

    $tag = Tag::factory()->create();

    $this->put(route('tags.update', $tag), [
        'name' => 'Updated Name',
    ])->assertRedirect();

    $this->assertDatabaseHas('tags', [
        'name' => 'Updated Name',
        'slug' => 'updated-name',
    ]);
});

test('an admin user can destroy tags', function () {
    $this->actingAs(User::factory([
        'id' => 1,
    ])->create());

    $tag = Tag::factory()->create();

    $this->delete(route('tags.destroy', $tag))
        ->assertRedirect();

    $this->assertDatabaseMissing('tags', [
        'id' => $tag->id,
    ]);
});
