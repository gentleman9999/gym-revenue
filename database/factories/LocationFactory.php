<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Locations\Projections\Location;
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
    public function definition(): array
    {
        $phone = preg_replace('/[^0-9]+/', '', $this->faker->phoneNumber);
        if (str_starts_with($phone, '1')) {
            $phone = substr($phone, 1);
        }

        return [
            'name' => $this->faker->streetName,
            'address1' => $this->faker->streetAddress,
            'address2' => $this->faker->secondaryAddress,
            'city' => $this->faker->city,
            'location_no' => $this->faker->buildingNumber,
            'state' => $this->faker->stateAbbr,
            'zip' => substr($this->faker->postcode, 0, 5),
            'phone' => $phone,
        ];
    }
}
