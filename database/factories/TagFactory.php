<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
        ];
    }
}
