<?php

declare(strict_types=1);

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\EntrySourceCategories\Actions\CreateEntrySourceCategory;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class EntrySourceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $escs = [
            'vip' => 'VIP Leads',
            'digitally-produced' => 'Digitally Produced Leads',
            'personally-produced' => 'Personally Produced Leads',
        ];

        $clients = Client::all();
        foreach ($clients as $client) {
            foreach ($escs as $esc => $readable_source) {
                CreateEntrySourceCategory::run([
                    'name' => $readable_source,
                    'value' => $esc,
                    'client_id' => $client->id,
                ]);


                VarDumper::dump("Adding Entry Source Category {$readable_source}");
            }
        }
    }
}
