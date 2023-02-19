<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovieStoreRequest;
use App\Http\Requests\MovieUpdateRequest;
use App\Models\Movie;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $movie = Movie::with(['user'])->latest()->paginate(5);
        
            return response()->json($movie, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieStoreRequest $movieStoreRequest)
    {
        try {
            $imageName = '';
            if($movieStoreRequest->file('image'))
            {
                $file = $movieStoreRequest->file('image');
                $imageName = time().$file->getClientOriginalName();
                $file->move(public_path('images'), $imageName);
            }

            $validInput = $movieStoreRequest->validated();
            
            $movie = Movie::create([
                'title' => $validInput['title'],
                'description' => $movieStoreRequest->description,
                'rating' => $validInput['rating'],
                'image' => $imageName
            ]);

            return response()->json($movie, Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        try {
            return response()->json($movie, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieUpdateRequest $movieUpdateRequest, Movie $movie)
    {
        try {
            $updateData = [];
            if ($movieUpdateRequest->file('image')) {
        
                if ($movie->image) {
                    $imageName = $movie->image;
                    unlink(public_path('images/'.$imageName));
                }

                $file = $movieUpdateRequest->file('image');
                $imageName = time().$file->getClientOriginalName();
                $file->move(public_path('images'), $imageName);
                $updateData['image'] = $imageName;
            }

            $validInput = $movieUpdateRequest->validated();

            $updateData['title'] = $validInput['title'];
            $updateData['description'] = $movieUpdateRequest->description;
            $updateData['rating'] = $validInput['rating'];

            $movie->update($updateData);

            return response()->json($movie, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        try {
            if ($movie->image) {
                $imageName = $movie->image;
                unlink(public_path('images/'.$imageName));
            }
            $movie->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
