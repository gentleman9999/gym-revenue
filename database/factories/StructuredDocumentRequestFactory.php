<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Clients\Actions\CreateClient;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Actions\CreateLocation;
use App\Domain\Locations\Projections\Location;
use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use App\Domain\StructuredDocuments\Actions\CreateStructuredDocument;
use App\Domain\StructuredDocuments\Projections\StructuredDocument;

use Illuminate\Database\Eloquent\Factories\Factory;

class StructuredDocumentRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StructuredDocumentRequest::class;

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

        $structured_document_id = CreateStructuredDocument::run(StructuredDocument::factory()->raw())->id;

        return [
            'client_id' => $client_id,
            'structured_document_id' => $structured_document_id,
            'entity_id' => $entity_id,
            'entity_type' => Location::class,
        ];
    }
}
