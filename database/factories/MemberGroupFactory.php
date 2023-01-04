<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\MemberGroups\Projections\MemberGroup;
use App\Enums\MemberGroupTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class MemberGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MemberGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $phone = preg_replace('/[^0-9]+/', '', $this->faker->phoneNumber);
        if (str_starts_with($phone, '1')) {
            $phone = substr($phone, 1);
        }

        return [
            'type' => MemberGroupTypeEnum::FAMILY,
            'poc_name' => $this->faker->name,
            'poc_email' => $this->faker->unique()->safeEmail,
            'poc_phone' => $phone,
        ];
    }
}
