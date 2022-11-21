<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Label;
use App\Models\Project;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $client = new Client();
//        $continentData = $client->request('GET', 'http://country.io/continent.json');
//        $continentsCountries = json_decode($continentData->getBody(), true);
//        $continents = array_unique($continentsCountries);
//        foreach ($continents as $key => $value)
//        {
//            Continent::create(['code' => $value]);
//        }
//        $countryData = $client->request('GET', 'http://country.io/names.json');
//        $countries = json_decode($countryData->getBody(), true);
//        foreach ($countries as $key => $value)
//        {
//            $continent = Continent::firstWhere('code', $continentsCountries[$key]);
//            Country::create([
//               'code' => $key,
//               'name' => $value,
//               'continent_id' => $continent->id
//            ]);
//        }
        $users = User::factory(10)->create();
        $projects = Project::factory(10)->create();
        $labels = Label::factory(20)->make()->each(function ($label) use ($users){
            $label->user_id = $users->random()->id;
            $label->save();
        });
        $labels->each(function($label) use ($projects){
            $label->projects()->attach($projects->random(rand(1,4))->pluck('id'));
        });
        $projects->each(function ($project) use ($users){
            $project->users()->attach($users->random(rand(2, 6))->pluck('id'));
        });
    }
}
