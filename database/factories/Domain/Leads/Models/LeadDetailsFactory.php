<?php

namespace Database\Factories\Domain\Leads\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;
use Symfony\Component\VarDumper\VarDumper;

class LeadDetailsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Domain\Leads\Models\LeadDetails::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        VarDumper::dump('Generating Lead Details');
        $email = $this->faker->email();

        return [
            'id' => Uuid::uuid4()->toString(),
            'field' => $this->faker->randomElement(['called_by_rep', 'sms_by_rep', 'emailed_by_rep']),
            'created_at' => $this->faker->dateTimeThisMonth(),
            'active' => true,
            'value' => $this->faker->dateTimeThisMonth(),
            'misc' => [
                'notes' => $this->faker->paragraph(),
                'subject' => $this->faker->sentence(),
                'message' => $this->faker->paragraph(6),
                'outcome' => $this->faker->randomElement([
                    'contacted',
                    'voicemail',
                    'hung-up',
                    'wrong-number',
                    'appointment',
                    'sale',
                ]),
                "user" => ['email' => $email],
            ],
        ];
    }

    public function client_id(string $client_id)
    {
        return $this->state(function (array $attrs) use ($client_id) {
            return [
                'client_id' => $client_id,
            ];
        });
    }

    public function lead_id(string $lead_id)
    {
        return $this->state(function (array $attrs) use ($lead_id) {
            return [
                'lead_id' => $lead_id,
            ];
        });
    }
}
