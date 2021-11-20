<?php

namespace Database\Factories\Endusers;

use App\Models\Endusers\LeadDetails;
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
    protected $model = LeadDetails::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'field' => $this->faker->randomElement(['called_by_rep', 'sms_by_rep', 'emailed_by_rep']),
            'created_at' => $this->faker->dateTimeThisMonth(),
            'active' => true,
            'misc' => [
                'notes' => $this->faker->paragraph(),
                'message' => $this->faker->paragraph(),
                'outcome' => $this->faker->randomElement([
                    'contacted',
                    'voicemail',
                    'hung-up',
                    'wrong-number',
                    'appointment',
                    'sale'
                ]),
                "user" => ['email' => $this->faker->email()]
            ]
        ];
    }

    public function client_id(string $client_id)
    {
        return $this->state(function (array $attrs) use ($client_id) {
            return [
                'client_id' => $client_id
            ];
        });
    }

    public function lead_id(string $lead_id)
    {
        return $this->state(function (array $attrs) use ($lead_id) {
            return [
                'lead_id' => $lead_id
            ];
        });
    }
}
