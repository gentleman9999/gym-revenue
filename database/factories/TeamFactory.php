<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 * @method static array factory()
 */
class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'client_id' => Client::factory(),
            'name' => $this->faker->unique()->company,
            'home_team' => 1,
        ];
    }
}
