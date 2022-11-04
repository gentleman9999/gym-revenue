<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Audiences\Audience;
use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class AudienceFactory extends AbstractProjectionFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Audience::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'entity' => Lead::class,
            'filters' => null,
            'client_id' => Client::factory(),
        ];
    }
}
