<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Clients\Actions\CreateClient;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Actions\CreateLocation;
use App\Domain\Locations\Projections\Location;
use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class StructuredDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StructuredDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $client_id = CreateClient::run(Client::factory()->raw())->id;

        $entity_id = CreateLocation::run(Location::factory()->raw() + [
            'shouldCreateTeam' => true,
            'client_id' => $client_id,
        ])->id;

        return [
            'template_file_id' => File::factory()->create()->id,
            'client_id' => $client_id,
            'entity_id' => $entity_id,
            'entity_type' => Location::class,
            'ttl' => now(),
        ];
    }
}
