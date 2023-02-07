<?php

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\EntrySources\Actions\CreateEntrySource;
use Illuminate\Database\Seeder;

class EntrySourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entry_sources = [
            'walk-in' => 'Walk In',
            'buddy-referral' => 'Buddy Referral',
            'member-guest' => 'Member Guest Pass',
            'facebook' => 'Meta/Facebook/Instagram',
            'guest-pass' => 'Guest Pass',
            'custom' => 'Custom',
        ];

        $clients = Client::all();
        foreach ($clients as $client) {
            foreach ($entry_sources as $entry_source => $readable_source) {
                CreateEntrySource::run([
                    'name' => $readable_source,
                    'source' => $entry_source,
                    'ui' => 1,
                    'client_id' => $client->id,
                ]);


                echo "Adding Entry source {$readable_source}\n";
            }
        }
    }
}
