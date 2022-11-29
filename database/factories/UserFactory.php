<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phone = preg_replace('/[^0-9]+/', '', $this->faker->phoneNumber);
        if (str_starts_with($phone, '1')) {
            $phone = substr($phone, 1);
        }

        return [
            'first_name' => $first_name = $this->faker->firstName,
            'last_name' => $last_name = $this->faker->lastName,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'address1' => $this->faker->streetAddress,
            'address2' => $this->faker->secondaryAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'zip' => substr($this->faker->postcode, 0, 5),
            'alternate_email' => $this->faker->email,
            'email' => "{$first_name}.{$last_name}@{$this->faker->freeEmailDomain}",
            'phone' => $phone,
            'role_id' => 1,
        ];
    }

    public function configure(): Factory
    {
        return $this->afterMaking(function (User $user) {
            $user->team_id ??= Team::factory()->create()->id;

            return $user;
        });
    }
}
