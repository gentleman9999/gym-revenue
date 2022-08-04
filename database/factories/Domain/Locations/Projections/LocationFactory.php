<?php

namespace Database\Factories\Domain\Locations\Projections;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Domain\Locations\Projections\Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $phone = str(preg_replace('/[^0-9.]+/', '', $this->faker->phoneNumber));
        if ($phone->startsWith("1")) {
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
