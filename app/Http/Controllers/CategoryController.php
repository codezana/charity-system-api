<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Category, User};
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
          /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Display categories
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate and create categorie
        $validator = Validator::make($request->all(), ([
            'name'=> 'required|string',
        ]));

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        // Create the categorie
        $categorie = Category::create($validator->validated());

        return response()->json([
            'message' => 'جۆر بە سەرکەوتوویی دروستکرا',
            'categorie' => $categorie
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Show specific categorie
        $categorie = Category::findOrFail($id);
        return response()->json($categorie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        // Validate input
        $validated = Validator::make($request->all(), ([
            'name'=> 'string',
        ]));

        if ($validated->fails()) {
            return response()->json([
                'error' => collect($validated->errors()->all())->first()
            ], 422);
        }
        // Find the expense
        $categorie = Category::findOrFail($id);

        $validateData = $validated->validated();
        // Update only provided fields
        $categorie->update($validateData);

        return response()->json([
            'message' => 'جۆر بە سەرکەوتوویی نوێکرایەوە',
            'categorie' => $categorie
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Delete categorie
        $categorie = Category::findOrFail($id);

        $categorie->delete();

        return response()->json(['message' => 'جۆر بە سەرکەوتوویی سڕایەوە']);
    }
}
