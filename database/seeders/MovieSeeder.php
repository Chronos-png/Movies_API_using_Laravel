<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = Movie::factory(50)->create();

        $genres = MovieGenre::all();

        foreach ($movies as $movie) {
            $randomGenres = $genres->random(rand(1, 3))->pluck('id');
            $movie->genres()->attach($randomGenres);
        }
    }
}
