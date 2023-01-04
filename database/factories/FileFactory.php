<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Clients\Actions\CreateClient;
use App\Domain\Clients\Projections\Client;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $client_id = CreateClient::run(Client::factory()->raw())->id;
        $filename = $this->faker->name;
        $extension = $this->faker->randomElement(['pdf', 'docx', 'png', 'jpeg']);

        return [
            'id' => $this->faker->uuid,
            'client_id' => $client_id,
            'filename' => $filename,
            'original_filename' => $filename,
            'extension' => $extension,
            'bucket' => 's3',
            'key' => $client_id.'/'.$filename.'.'.$extension,
            'size' => rand(10000, 99999),
        ];
    }
}
