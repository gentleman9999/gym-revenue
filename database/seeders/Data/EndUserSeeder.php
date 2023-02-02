<?php

declare(strict_types=1);

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use App\Services\Process;
use Illuminate\Database\Seeder;

class EndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $amount_of_leads = env('RAPID_SEED') ? 3 : 20;
        if (env('QUICK_SEED') === true) {
            $amount_of_leads = 1;
        }
        // Get all the Clients
        $clients = Client::with('locations')->whereActive(1)->get();
        $process = Process::allocate(5);

        foreach ($clients as $client) {
            $process->queue([self::class, 'creatUsers'], $client->toArray(), $amount_of_leads);
        }

        $process->run();
    }

    public static function creatUsers(array $client, int $amount_of_leads): void
    {
        $user_types = [
            UserTypesEnum::LEAD,
            UserTypesEnum::CUSTOMER,
            UserTypesEnum::MEMBER,
        ];

        foreach ($client['locations'] as $location) {
            // For each location, MAKE 25 users, don't create
            foreach ($user_types as $user_type) {
                $end_users = User::factory()->count($amount_of_leads)->make()->toArray();

                foreach ($end_users as $end_user_data) {
                    if (User::where(['client_id' => $client['id'], 'email' => $end_user_data['email']])->exists()) {
                        continue;
                    }

                    $end_user_data['client_id']        = $client['id'];
                    $end_user_data['home_location_id'] = $location['gymrevenue_id'];
                    $end_user_data['user_type']        = $user_type;
                    $end_user_data['password_hashed']  = '$2y$10$S9YUvKwjvgTuj8K5wXlqTOBUDJ0hOEk/dhBoVAm/vYVchKS/taQt2'; // Hello123!

                    if ($user_type == 'lead') {
                        $end_user_data['opportunity'] = rand(0, 3);
                        $end_user_data['opportunity'] = $end_user_data['opportunity'] == 0 ? null : $end_user_data['opportunity'];
                    }

                    CreateUser::run($end_user_data);
                }
            }
        }

        echo "Generated End Users for {$client['name']}!\n";
    }
}
