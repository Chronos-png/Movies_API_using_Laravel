<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;


class MovieController extends Controller
{
    /**
     * Get all movies.
     *
     * @OA\Get(
     *     path="/api/movies",
     *     tags={"Movies"},
     *     security={{"bearer_token":{}}},
     *     summary="Retrieve all movies with genres",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Movie"))
     *     )
     * )
     */
    public function index()
    {
        $movies = Movie::with('genres')->get();
        return response()->json($movies);
    }




    /**
     * Store a newly created movie.
     *
     * @OA\Post(
     *     path="/api/movies",
     *     tags={"Movies"},
     *     security={{"bearer_token":{}}},
     *     summary="Create a new movie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "release_year"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="release_year", type="integer"),
     *             @OA\Property(property="genres", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Movie")),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_year' => 'required|integer',
            'genres' => 'array',
            'genres.*' => 'integer|exists:movie_genres,id'
        ]);

        $movie = Movie::create($request->only(['title', 'description', 'release_year']));

        if ($request->has('genres')) {
            $movie->genres()->attach($request->genres);
        }

        return response()->json($movie->load('genres'), 201);
    }

    /**
     * Get a specific movie by ID.
     *
     * @OA\Get(
     *     path="/api/movies/{id}",
     *     tags={"Movies"},
     *     security={{"bearer_token":{}}},
     *     summary="Get a movie by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/Movie")),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Movie $movie)
    {
        return response()->json($movie->load('genres'));
    }

    /**
     * Update an existing movie.
     *
     * @OA\Put(
     *     path="/api/movies/{id}",
     *     tags={"Movies"},
     *     security={{"bearer_token":{}}},
     *     summary="Update a movie",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="release_year", type="integer"),
     *             @OA\Property(property="genres", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/Movie")),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, Movie $movie)
    {
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'release_year' => 'integer',
            'genres' => 'array',
            'genres.*' => 'integer|exists:movie_genres,id'
        ]);

        $movie->update($request->only(['title', 'description', 'release_year']));

        if ($request->has('genres')) {
            $movie->genres()->sync($request->genres);
        }

        return response()->json($movie->load('genres'));
    }

    /**
     * Delete a movie.
     *
     * @OA\Delete(
     *     path="/api/movies/{id}",
     *     tags={"Movies"},
     *     security={{"bearer_token":{}}},
     *     summary="Delete a movie",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="No Content"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Movie $movie)
    {
        $movie->delete();
        return response()->json(null, 204);
    }
}
