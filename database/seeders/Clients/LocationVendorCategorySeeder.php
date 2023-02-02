<?php

namespace Database\Seeders\Clients;

use App\Domain\Clients\Projections\Client;
use App\Domain\LocationVendorCategories\Actions\CreateLocationVendorCategory;
use Illuminate\Database\Seeder;

class LocationVendorCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();
        $category_names = ['Vendor Category 1','Vendor Category 2','Vendor Category 3'];
        foreach ($clients as $client) {
            echo("Adding Location vendor categories for client {$client->name}\n");
            foreach ($category_names as $category_name) {
                CreateLocationVendorCategory::run([
                    'name' => $category_name,
                    'client_id' => $client->id,
                ]);
            }
        }
    }
}
