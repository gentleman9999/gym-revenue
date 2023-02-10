<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Actions\Nicknames\FindNickname;
use App\Domain\Users\Models\User;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Find potential duplicate users
 */
class FindDuplicateUsers
{
    use AsAction;

    public const SCORES = [
        'email' => 1,
        'driving_license' => 1,
        'name' => 0.33,
        'birthday' => 0.3,
        'phone' => 0.3,
        'zip' => 0.06,
    ];

    /**
     * Function to handle find duplicate users action
     *
     * @param array<string, mixed> $data Data of user input
     *
     * @return Collection return Collection of Users
     * @access public
     */
    public function handle(array $data): Collection
    {
        if (! $this->arrayHasRequiredKeys($data)) {
            return [];
        }

        $query = (new User())->where('id');

        if (isset($data['email'])) {
            $email = $this->formatEmail($data['email']);

            $query = $query->orWhere(
                function ($q) use ($email): void {
                    $q->whereRaw(
                        "regexp_replace(email, '\\\\+[^@].*@+', '@') = '$email'"
                    )->orWhereRaw(
                        "regexp_replace(alternate_emails, '\\\\+[^@].*@+', '@') = '$email'"
                    );
                }
            );
        }

        if (isset($data['first_name'])) {
            $nicknames = FindNickname::run($data['first_name'])->toArray();

            $query = $query->orWhere(function ($query) use ($nicknames): void {
                $query->whereIn('first_name', $nicknames);
            });
        }

        if (isset($data['last_name'])) {
            $query = $query->orWhere('last_name', $data['last_name']);
        }

        if (isset($data['phone'])) {
            $phone = $data['phone'];

            $query = $query->orWhere('phone', $phone);
        }

        if (isset($data['zip'])) {
            $query = $query->orWhere('zip', $data['zip']);
        }

        $potential_users = $query->get();

        return $this->getUsersByScore($data, $potential_users);
    }

    /**
     * Function to find if the input data has
     * any of the required keys
     *
     * @param array<string, mixed> $data Data of user input
     *
     * @access private
     */
    private function arrayHasRequiredKeys(array $data): bool
    {
        $keys = [
            'email', 'drivers_license', 'first_name', 'last_name', 'date_of_birth', 'phone', 'zip',
        ];

        return count(array_intersect_key(array_flip($keys), $data)) > 0;
    }

    /**
     * Format email and removes texts after
     * plus (+) sign if there is any
     *
     * @param string $email Inpit Email
     *
     */
    private function formatEmail(string $email): string
    {
        return preg_replace('/(\+)(.*?)(?=\@)/', '', $email);
    }

    private function isMatchNickname(string $input_name, string $user_name): bool
    {
        $nicknames = FindNickname::run($input_name)->toArray();

        return in_array(strtolower($user_name), array_map('strtolower', $nicknames));
    }

    /**
     * Function to score and order all users
     *
     * @param array<string, mixed>      $data  Data of user input
     * @param Collection $users Collection of users
     *
     * @access private
     */
    private function getUsersByScore(array $data, Collection $users): Collection
    {
        return $users->map(
            function (User $user) use ($data) {
                $score = 0;

                $input_email = $this->formatEmail($data['email']);
                $email       = $this->formatEmail($user->email);
                $user_emails = [$email];

                if ($user->alternate_emails) {
                    foreach ($user->alternate_emails as $alternate_email) {
                        $user_emails[] = $this->formatEmail($alternate_email);
                    }
                }

                if (in_array($input_email, $user_emails)) {
                    $score += FindDuplicateUsers::SCORES['email'];
                }

                if (
                    $data['last_name'] === $user->last_name
                    && $this->isMatchNickname($data['first_name'], $user->first_name)
                ) {
                    $score += FindDuplicateUsers::SCORES['name'];
                }

                if (in_array($data['phone'], [$user->primary_phone, $user->alternate_phone])) {
                    $score += FindDuplicateUsers::SCORES['phone'];
                }

                if ($data['zip'] == $user->zip) {
                    $score += FindDuplicateUsers::SCORES['zip'];
                }

                $user->score = $score;

                return $user;
            }
        )
        ->sortBy('score')
        ->take(3);
    }
}
