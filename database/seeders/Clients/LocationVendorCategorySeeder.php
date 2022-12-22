<?php

namespace Database\Seeders\Clients;

use App\Domain\Clients\Projections\Client;
use App\Domain\LocationVendorCategories\Actions\CreateLocationVendorCategory;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

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
        foreach ($clients as $idx => $client) {
            VarDumper::dump("Adding Location vendor categories for client {$client->name}");
            foreach ($category_names as $category_name) {
                $location_vendor_category['name'] = $category_name;
                $location_vendor_category['client_id'] = $client->id;
                CreateLocationVendorCategory::run($location_vendor_category);
            }
        }
    }
}
