<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Cases};

class CaseController extends Controller
{
             /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Display cases
        $cases = Cases::all();
        return response()->json($cases);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate and create case
        $validator = Validator::make($request->all(), ([
            'name'=> 'required|string',
        ]));

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        // Create the case
        $case = Cases::create($validator->validated());

        return response()->json([
            'message' => 'کەیس بە سەرکەوتوویی دروستکرا',
            'case' => $case   
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Show specific case
        $case = Cases::findOrFail($id);
        return response()->json($case);
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
        $case = Cases::findOrFail($id);

        $validateData = $validated->validated();
        // Update only provided fields
        $case->update($validateData);

        return response()->json([
            'message' => 'کەیس بە سەرکەوتوویی نوێکرایەوە',
            'case' => $case
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Delete case
        $case = Cases::findOrFail($id);

        $case->delete();

        return response()->json(['message' => 'کەیس بە سەرکەوتوویی سڕایەوە']);
    }
}
