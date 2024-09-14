<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use User;

class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'token' => Str::random(10),
            'is_public' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'vault_id' => Vault::factory(),
            'user_id' => User::factory(),
        ];
    }
}
