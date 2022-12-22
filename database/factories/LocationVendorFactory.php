<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\LocationVendors\Projections\LocationVendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationVendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LocationVendor::class;

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
            'name' => $this->faker->name,
            'poc_name' => $this->faker->name,
            'poc_email' => $this->faker->unique()->safeEmail,
            'poc_phone' => $phone,
        ];
    }
}
