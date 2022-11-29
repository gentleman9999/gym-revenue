<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Clients\Projections\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 * @method static array factory()
 */
class ClientFactory extends AbstractProjectionFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'name' => $company = $this->faker->company,
            'active' => 1,
            'prefix' => preg_replace('/[^A-Z]+/', '', $company),
            'services' => [
                'FREE_TRIAL',
                'MASS_COMMS',
                'CALENDAR',
                'LEADS',
                'MEMBERS',
            ],
        ];
    }
}
