<?php

declare(strict_types=1);

namespace Database\Seeders\Clients;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Actions\CreateLocation;
use App\Domain\Locations\Actions\ImportLocations;
use App\Domain\Locations\Enums\LocationType;
use App\Domain\Locations\Projections\Location;
use App\Services\Process;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\VarDumper\VarDumper;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('RAPID_SEED') === true) {
            $locations = [
                // The Kalamazoo
                [
                    'client' => 'The Kalamazoo',
                    'name' => 'The Kalamazoo 1 HQ',
                    'state' => 'LA',
                    'city' => 'Shreveport',
                    'zip' => '71101',
                    'location_no' => '001',
                    'location_type' => LocationType::HQ,
                    //'gymrevenue_id' => 'TK12'
                ],
            ];
        } else {
            $locations = [
                // FitnessTruth
                [
                    'client' => 'FitnessTruth',
                    'name' => 'FitnessTruth 1',
                    'state' => 'TX',
                    'city' => 'Abbott',
                    'zip' => '76621',
                    'location_no' => 'FT13',
                    'location_type' => LocationType::OFFICE,
                ],
                [
                    'client' => 'FitnessTruth',
                    'name' => 'FitnessTruth 2',
                    'state' => 'TX',
                    'city' => 'Abernathy',
                    'zip' => '79311',
                    'location_no' => 'FT14',
                    'location_type' => LocationType::OFFICE,
                ],
                [
                    'client' => 'FitnessTruth',
                    'name' => 'FitnessTruth 3',
                    'state' => 'TX',
                    'city' => 'Abram',
                    'zip' => '78572',
                    'location_no' => 'FT15',
                    'location_type' => LocationType::OFFICE,
                ],
                [
                    'client' => 'FitnessTruth',
                    'name' => 'FitnessTruth 4',
                    'state' => 'TX',
                    'city' => 'Abbott',
                    'zip' => '76621',
                    'location_no' => 'FT16',
                    'location_type' => LocationType::OFFICE,
                ],
                [
                    'client' => 'FitnessTruth',
                    'name' => 'FitnessTruth 5',
                    'state' => 'TX',
                    'city' => 'Abernathy',
                    'zip' => '79311',
                    'location_no' => 'FT17',
                    'location_type' => LocationType::STORE,
                ],
                [
                    'client' => 'FitnessTruth',
                    'name' => 'FitnessTruth 6',
                    'state' => 'TX',
                    'city' => 'Abbott',
                    'zip' => '76621',
                    'location_no' => 'FT18',
                    'location_type' => LocationType::STORE,
                ],
                // The Z
                [
                    'client' => 'The Z',
                    'name' => 'Zoo 1',
                    'state' => 'HI',
                    'city' => 'Honolulu',
                    'zip' => '96795',
                    'location_no' => 'TZ01',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'TZ01'
                ],
                [
                    'client' => 'The Z',
                    'name' => 'Zoo 3',
                    'state' => 'HI',
                    'city' => 'Honolulu',
                    'zip' => '96796',
                    'location_no' => 'TZ03',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'TZ03'
                ],
                [
                    'client' => 'The Z',
                    'name' => 'Zoo 4',
                    'state' => 'HI',
                    'city' => 'Honolulu',
                    'zip' => '96797',
                    'location_no' => 'TZ04',
                    'location_type' => LocationType::STORE,
                    // 'gymrevenue_id' => 'TZ04'
                ],
                [
                    'client' => 'The Z',
                    'name' => 'Zoo 6',
                    'state' => 'HI',
                    'city' => 'Waimanalo',
                    'zip' => '96795',
                    'location_no' => 'TZ06',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'TZ06'
                ],

                // Stencils
                [
                    'client' => 'Stencils',
                    'name' => 'Stencils 1',
                    'state' => 'CA',
                    'city' => 'Los Angeles',
                    'zip' => '90001',
                    'location_no' => 'ST01',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'ST07'
                ],
                [
                    'client' => 'Stencils',
                    'name' => 'Stencils 2',
                    'state' => 'CA',
                    'city' => 'San Diego',
                    'zip' => '22400',
                    'location_no' => 'ST02',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'ST08'
                ],
                [
                    'client' => 'Stencils',
                    'name' => 'Stencils @ Microsoft',
                    'state' => 'Wa',
                    'city' => 'Redmond',
                    'zip' => '98052',
                    'location_no' => 'STMS',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'ST11'
                ],
                // SciFi Purple Gyms
                [
                    'client' => 'Sci-Fi Purple Gyms',
                    'state' => 'NC',
                    'city' => 'Advance',
                    'zip' => '27006',
                    'location_no' => 'PF01',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'PF26'
                ],
                [
                    'client' => 'Sci-Fi Purple Gyms',
                    'state' => 'NC',
                    'city' => 'Advance',
                    'zip' => '27006',
                    'location_no' => 'PF02',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'PF27'
                ],
                [
                    'client' => 'Sci-Fi Purple Gyms',
                    'state' => 'FL',
                    'city' => 'Ponte Vedra Beach',
                    'zip' => '32004',
                    'location_no' => 'PF03',
                    'location_type' => LocationType::STORE,
                    //'gymrevenue_id' => 'PF28'
                ],
            ];
        }

        if (! App::environment(['production', 'staging']) && ! env('RAPID_SEED', false)) {
            $locations[] = [
                'client' => 'TruFit Athletic Clubs',
                'name' => 'TruFit 1',
                'state' => 'TX',
                'city' => 'Amarillo',
                'zip' => '79106',
                'location_no' => 'TR66',
                'location_type' => LocationType::STORE,
                //'gymrevenue_id' => 'ST07'
            ];
            $locations[] = [
                'client' => 'TruFit Athletic Clubs',
                'name' => 'TruFit 2',
                'state' => 'TN',
                'city' => 'Antioch',
                'zip' => '37013',
                'location_no' => 'TR77',
                'location_type' => LocationType::STORE,
                //'gymrevenue_id' => 'ST07'
            ];
        }

        $clients = [];
        $min_opened_at = Carbon::now()->subDays(3650)->timestamp;
        $max_opened_at = Carbon::now()->subDays(365)->timestamp;
        $min = Carbon::now()->subDays(365)->timestamp;
        $temp_data = Location::factory()->count(count($locations))->make()->toArray();
        $process = Process::allocate(5);

        foreach ($locations as $idx => $location) {
            $client = $clients[$location['client']] ?? $clients[$location['client']] = Client::whereName($location['client'])->first();

            $location['name'] = $location['name'] ?? $location['client'] . " " . ($idx + 1);
            $location['client_id'] = $client->id;


            $val_opened_at = rand($min_opened_at, $max_opened_at);
            $opened_at = date('Y-m-d H:i:s', $val_opened_at);
            $location['opened_at'] = $opened_at;

            $date_opened_at = \DateTime::createFromFormat('Y-m-d H:i:s', $opened_at);
            $min_presale_opened_at = Carbon::create($date_opened_at->format('Y'), $date_opened_at->format('m'), $date_opened_at->format('d'))->timestamp;
            $val_presale_opened_at = rand($min_presale_opened_at, $max_opened_at);
            $presale_opened_at = date('Y-m-d H:i:s', $val_presale_opened_at);
            $location['presale_opened_at'] = $presale_opened_at;

            $max = Carbon::now()->timestamp;
            $val = rand($min, $max);
            $presale_started_at = date('Y-m-d H:i:s', $val);
            $location['presale_started_at'] = $presale_started_at;

            unset($location['client']);
            $finalData = array_merge($temp_data[$idx], $location);
            $finalData['shouldCreateTeam'] = true;

            echo("Adding {$location['name']}\n");
            CreateLocation::run($finalData);
        }

        if (! App::environment(['production', 'staging']) && ! env('RAPID_SEED', false)) {
            $trufit_client_id = Client::whereName('TruFit Athletic Clubs')->first()->id;
            ///now do trufit csv import
            VarDumper::dump("Adding TruFit Locations from CSV");
            $key = 'tmp_data/trufit-clubs';

            $process->queue([self::class, 'uploadToS3'], $key, $trufit_client_id);
        }

        $process->run();
    }

    public static function uploadToS3(string $key, string $trufit_client_id)
    {
        $csv = file_get_contents(realpath(__DIR__."/../../../database/data/trufit-clubs.csv"));
        Storage::disk('s3')->put($key, $csv);
        Storage::disk('s3')->get($key);

        ImportLocations::run([
            [
                'key' => $key,
                'extension' => 'csv',
                'client_id' => $trufit_client_id,
            ],
        ]);
    }
}
