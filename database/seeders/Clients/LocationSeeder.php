<?php

namespace Database\Seeders\Clients;

use App\Actions\Clients\Locations\CreateLocation;
use App\Actions\Clients\Locations\GenerateGymRevenueId;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use Illuminate\Database\Seeder;
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
        $locations = [
            // The Kalamazoo
            [
                'client_id' => 'The Kalamazoo',
                'name' => 'The Kalamazoo 1',
                'state' => 'LA',
                'city' => 'Shreveport',
                'zip' => '71101',
                'location_no' => '001',
                //'gymrevenue_id' => 'TK12'
            ],
            // Bodies By Brett
            [
                'client_id' => 'Bodies By Brett',
                'name' => 'Tampa 1',
                'state' => 'FL',
                'city' => 'Tampa',
                'zip' => '33607',
                'location_no' => '001',
                //'gymrevenue_id' => 'BB23'
            ],
            [
                'client_id' => 'Bodies By Brett',
                'name' => 'Tampa 2',
                'state' => 'FL',
                'city' => 'Tampa',
                'zip' => '33607',
                'location_no' => '002',
                //'gymrevenue_id' => 'BB24'
            ],
            [
                'client_id' => 'Bodies By Brett',
                'name' => 'Tampa 3',
                'state' => 'FL',
                'city' => 'Tampa',
                'zip' => '33607',
                'location_no' => '003',
                //'gymrevenue_id' => 'BB25'
            ],
            // FitnessTruth
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abbott',
                'zip' => '76621',
                'location_no' => 'TR22',
                //'gymrevenue_id' => 'FT13'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abernathy',
                'zip' => '79311',
                'location_no' => 'TR32',
                //'gymrevenue_id' => 'FT14'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abram',
                'zip' => '78572',
                'location_no' => 'TR33',
                //'gymrevenue_id' => 'FT15'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abbott',
                'zip' => '76621',
                'location_no' => 'TR34',
                //'gymrevenue_id' => 'FT16'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abernathy',
                'zip' => '79311',
                'location_no' => 'TR55',
                //'gymrevenue_id' => 'FT17'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abbott',
                'zip' => '76621',
                'location_no' => 'TR36',
                //'gymrevenue_id' => 'FT18'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abbott',
                'zip' => '76621',
                'location_no' => 'TR32',
                //'gymrevenue_id' => 'FT19'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abernathy',
                'zip' => '79311',
                'location_no' => 'TR40',
                //'gymrevenue_id' => 'FT20'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TX',
                'city' => 'Abbott',
                'zip' => '76621',
                'location_no' => 'TR65',
                //'gymrevenue_id' => 'FT21'
            ],
            [
                'client_id' => 'FitnessTruth',
                'state' => 'TN',
                'city' => 'Adams',
                'zip' => '37010',
                'location_no' => 'TR70',
                //'gymrevenue_id' => 'FT22'
            ],
            // The Z
            [
                'client_id' => 'The Z',
                'state' => 'HI',
                'city' => 'Honolulu',
                'zip' => '96795',
                'location_no' => 'TZ01',
                //'gymrevenue_id' => 'TZ01'
            ],
            [
                'client_id' => 'The Z',
                'state' => 'HI',
                'city' => 'Honolulu',
                'zip' => '96795',
                'location_no' => 'TZ02',
                //'gymrevenue_id' => 'TZ02'
            ],
            [
                'client_id' => 'The Z',
                'state' => 'HI',
                'city' => 'Honolulu',
                'zip' => '96796',
                'location_no' => 'TZ03',
                //'gymrevenue_id' => 'TZ03'
            ],
            [
                'client_id' => 'The Z',
                'state' => 'HI',
                'city' => 'Honolulu',
                'zip' => '96797',
                'location_no' => 'TZ04',
               // 'gymrevenue_id' => 'TZ04'
            ],
            [
                'client_id' => 'The Z',
                'state' => 'HI',
                'city' => 'Honolulu',
                'zip' => '96798',
                'location_no' => 'TZ05',
                //'gymrevenue_id' => 'TZ05'
            ],
            [
                'client_id' => 'The Z',
                'state' => 'HI',
                'city' => 'Waimanalo',
                'zip' => '96795',
                'location_no' => 'TZ06',
                //'gymrevenue_id' => 'TZ06'
            ],
            // TruFit Athletic Clubs
            [
                'client_id' => 'TruFit Athletic Clubs',
                'state' => 'TX',
                'city' => 'Amarillo',
                'zip' => '79106',
                'location_no' => 'TR66',
                //'gymrevenue_id' => 'ST07'
            ],
            [
                'client_id' => 'TruFit Athletic Clubs',
                'state' => 'TN',
                'city' => 'Antioch',
                'zip' => '37013',
                'location_no' => 'TR77',
                //'gymrevenue_id' => 'ST07'
            ],

            // Stencils
            [
                'client_id' => 'Stencils',
                'state' => 'CA',
                'city' => 'Los Angeles',
                'zip' => '90001',
                'location_no' => 'ST01',
                //'gymrevenue_id' => 'ST07'
            ],
            [
                'client_id' => 'Stencils',
                'state' => 'CA',
                'city' => 'San Diego',
                'zip' => '22400',
                'location_no' => 'ST02',
                //'gymrevenue_id' => 'ST08'
            ],
            [
                'client_id' => 'Stencils',
                'state' => 'CA',
                'city' => 'San Jose',
                'zip' => '94088',
                'location_no' => 'ST03',
                //'gymrevenue_id' => 'ST09'
            ],
            [
                'client_id' => 'Stencils',
                'state' => 'OR',
                'city' => 'Portland',
                'zip' => '97035',
                'location_no' => 'ST05',
                //'gymrevenue_id' => 'ST10'
            ],
            [
                'client_id' => 'Stencils',
                'name' => 'Stencils @ Microsoft',
                'state' => 'Wa',
                'city' => 'Redmond',
                'zip' => '98052',
                'location_no' => 'STMS',
                //'gymrevenue_id' => 'ST11'
            ],
            [
                'client_id' => 'Stencils',
                'state' => 'OR',
                'city' => 'Portland',
                'zip' => '97035',
                'location_no' => 'ST05',
                //'gymrevenue_id' => 'ST11'
            ],
            // SciFi Purple Gyms
            [
                'client_id' => 'Sci-Fi Purple Gyms',
                'state' => 'NC',
                'city' => 'Advance',
                'zip' => '27006',
                'location_no' => 'PF01',
                //'gymrevenue_id' => 'PF26'
            ],
            [
                'client_id' => 'Sci-Fi Purple Gyms',
                'state' => 'NC',
                'city' => 'Advance',
                'zip' => '27006',
                'location_no' => 'PF02',
                //'gymrevenue_id' => 'PF27'
            ],
            [
                'client_id' => 'Sci-Fi Purple Gyms',
                'state' => 'FL',
                'city' => 'Ponte Vedra Beach',
                'zip' => '32004',
                'location_no' => 'PF03',
                //'gymrevenue_id' => 'PF28'
            ],
            [
                'client_id' => 'Sci-Fi Purple Gyms',
                'state' => 'FL',
                'city' => 'Callahan',
                'zip' => '32011',
                'location_no' => 'PF04',
                //'gymrevenue_id' => 'PF29'
            ],
            // iFit
            [
                'client_id' => 'iFit',
                'state' => 'FL',
                'city' => 'Tampa',
                'zip' => '33605',
                'location_no' => 'I01',
                //'gymrevenue_id' => 'IF30'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'FL',
                'city' => 'Lake City',
                'zip' => '32025',
                'location_no' => 'I02',
                //'gymrevenue_id' => 'IF31'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'FL',
                'city' => 'Hilliard',
                'zip' => '32046',
                'location_no' => 'I03',
                //'gymrevenue_id' => 'IF32'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'FL',
                'city' => 'Orange Park',
                'zip' => '32065',
                'location_no' => 'I04',
                //'gymrevenue_id' => 'IF33'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'FL',
                'city' => 'Tampa',
                'zip' => '33601',
                'location_no' => 'I05',
                //'gymrevenue_id' => 'IF34'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'GA',
                'city' => 'Atlanta',
                'zip' => '30301',
                'location_no' => 'I06',
                //'gymrevenue_id' => 'IF35'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'GA',
                'city' => 'Atlanta',
                'zip' => '30302',
                'location_no' => 'I13',
                //'gymrevenue_id' => 'IF36'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'GA',
                'city' => 'Atlanta',
                'zip' => '30303',
                'location_no' => 'I12',
                //'gymrevenue_id' => 'IF37'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'VA',
                'city' => 'Virginia Beach',
                'zip' => '23450',
                'location_no' => 'I11',
                //'gymrevenue_id' => 'IF38'
            ],
            [
                'client_id' => 'iFit',
                'state' => 'VA',
                'city' => 'Virginia Beach',
                'zip' => '23451',
                'location_no' => 'I10',
                //'gymrevenue_id' => 'IF39'
            ],
        ];

        foreach($locations as $idx => $location)
        {
            $client = Client::whereName($location['client_id'])->first();
            $location['name'] = $location['name'] ?? $location['client_id']." ".($idx + 1);
            $location['client_id'] = $client->id;
            $location['gymrevenue_id'] = GenerateGymRevenueId::run($client->id);
            $loc_record = Location::whereGymrevenueId($location['gymrevenue_id'])->first();

            if(is_null($loc_record))
            {
                VarDumper::dump("Adding {$location['name']}");
                CreateLocation::run($location);
            }
            else
            {
                VarDumper::dump("Skipping {$location['name']}!");
            }
        }
    }
}
