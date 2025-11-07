<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'username'          => $this->faker->unique()->userName(),   // WAJIB
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role'              => 'user',                                // default
            'password'          => 'password',                            // auto-hash di casts
            'remember_token'    => \Str::random(10),
        ];
    }
}
