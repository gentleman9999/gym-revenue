<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Jetstream\Features;

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
     * @return array
     */
    public function definition()
    {
        $gender = $this->faker->randomElement(['male', 'female']) ;
        $first_name = $this->faker->firstName($gender);
        $last_name = $this->faker->lastName();
        $username = "{$first_name}.{$last_name}";
        $domain = $this->faker->freeEmailDomain;
        $alternate_email = "{$username}@{$domain}";
        $phone = preg_replace('/[^0-9.]+/', '', $this->faker->phoneNumber);
        if (str_starts_with("1", $phone)) {
            $phone = substr($phone, 1);
        }

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'name' => $first_name. ' ' .$last_name,
            'email_verified_at' => now(),
            'password' => bcrypt('Hello123!'), // password
            'address1' => $this->faker->streetAddress,
            'address2' => $this->faker->secondaryAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'zip' => trim($this->faker->postcode, 5),
            'alternate_email' => $alternate_email,
            'phone' => $phone,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     *
     * @return $this
     */
    public function withPersonalTeam()
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(function (array $attributes, User $user) {
                    return ['name' => $user->name.'\'s Team', 'user_id' => $user->id, 'personal_team' => true];
                }),
            'ownedTeams'
        );
    }

    public function configure()
    {
        return $this->afterMaking(function (User $user) {
            $user->email = strtolower($user->first_name). '.' .strtolower($user->last_name). '@' . str_replace(' ', '-', strtolower($user->client)) . '.com';

            return $user;
        });
    }
}
