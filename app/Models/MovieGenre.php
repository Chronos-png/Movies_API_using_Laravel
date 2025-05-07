<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Genre",
 *     title="Genre",
 *     description="Movie Genre",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string")
 * )
 */


class MovieGenre extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_movie_genre');
    }
}
