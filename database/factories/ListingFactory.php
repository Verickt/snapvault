<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'image_path' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'vault_id' => Vault::factory(),
        ];
    }
}
