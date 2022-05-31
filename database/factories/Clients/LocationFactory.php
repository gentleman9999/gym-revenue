<?php

namespace Database\Factories\Clients;

use App\Models\Clients\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $phone = preg_replace('/[^0-9.]+/', '', $this->faker->phoneNumber);
        if (str_starts_with("1", $phone)) {
            $phone = substr($phone, 1);
        }

        return [
            'address1' => $this->faker->streetAddress,
            'address2' => $this->faker->secondaryAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'zip' => trim($this->faker->postcode, 5),
            'phone' => $phone,
        ];
    }
}
