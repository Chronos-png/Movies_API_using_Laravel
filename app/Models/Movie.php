<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @OA\Schema(
 *     schema="Movie",
 *     title="Movie",
 *     description="Movie model",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="release_year", type="integer"),
 *     @OA\Property(property="genres", type="array", @OA\Items(type="object", ref="#/components/schemas/Genre"))
 * )
 */


class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'release_year'];

    public function genres()
    {
        return $this->belongsToMany(MovieGenre::class, 'movie_movie_genre', 'movie_id', 'movie_genre_id');
    }
}
