<?php

namespace Database\Factories;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $client = new Client();
        $continentData = $client->request('GET', 'http://country.io/continent.json');
        $continentsCountries = json_decode($continentData->getBody(), true);
        return [
            //
        ];
    }
}
