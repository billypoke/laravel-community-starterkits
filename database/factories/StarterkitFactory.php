<?php

namespace Database\Factories;

use App\Models\Starterkit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Starterkit>
 */
class StarterkitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'url' => $this->repoUrl(),
        ];
    }

    public function repoUrl(): string
    {
        [$first, $second] = $this->faker->words(2);

        return "https://github.com/$first/$second";
    }
}
