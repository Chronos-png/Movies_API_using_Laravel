<?php

namespace Database\Seeders;

use App\Models\MovieGenre;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MovieGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = ['Action', 'Comedy', 'Drama', 'Horror', 'Sci-Fi', 'Romance', 'Thriller', 'Adventure', 'Fantasy', 'Mystery'];

        foreach ($genres as $genre) {
            MovieGenre::create(['name' => $genre]);
        }
    }
}
