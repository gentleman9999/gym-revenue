<?php

namespace Database\Factories\Endusers;

use App\Models\Endusers\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lead::class;



    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $first_name = $this->faker->firstName();
        $last_name = $this->faker->lastName();
        $username = "{$first_name}.{$last_name}";
        $domain = $this->faker->freeEmailDomain;
        $email = "{$username}@{$domain}";
        return [
            'id' => Uuid::uuid4()->toString(),
            'client_id' => 0,
            'gr_location_id' => 'CB01',
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'mobile_phone' => preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '$1$2$3', $this->faker->phoneNumber()) ,
            'ip_address' => $this->faker->ipv4(),
            'lead_type' => 'free_trial'
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

    public function gr_location_id(string $gymrevenue_id)
    {
        return $this->state(function (array $attrs) use ($gymrevenue_id) {
            return [
                'gr_location_id' => $gymrevenue_id
            ];
        });

    }
}
