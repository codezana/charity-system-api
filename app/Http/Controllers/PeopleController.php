<?php

namespace App\Http\Controllers;

use App\Models\People;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeopleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Display people
        $people = People::with('project', 'user', 'cases')->get();
        return response()->json($people);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate and create people
        $validator = Validator::make($request->all(), ([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'project_id' => 'required|numeric|exists:projects,id',
            'case_id' => 'nullable|numeric|exists:cases,id',
        ]));

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        $validateData = $validator->validated();

        // Create the people
        $people = People::create([
            'project_id' => $validateData['project_id'],
            'made' => auth()->user()->id,
            'name' => $validateData['name'],
            'phone' => $validateData['phone'],
            'address' => $validateData['address'],
            'case_id' => $validateData['case_id'],
        ]);

        // Load relationships
        $people->load(['project', 'user', 'cases']);

        return response()->json([
            'message' => 'سودمەند بە سەرکەوتوویی دروستکرا',
            'people' => $people
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Show specific people
        $people = People::with('project', 'user', 'cases')->findOrFail($id);
        return response()->json($people);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate input
        $validated = Validator::make($request->all(), ([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'project_id' => 'sometimes|required|numeric|exists:projects,id',
            'case_id' => 'nullable|numeric|exists:cases,id',
        ]));

        if ($validated->fails()) {
            return response()->json([
                'error' => collect($validated->errors()->all())->first()
            ], 422);
        }
        // Find the people
        $people = People::findOrFail($id);

        $validateData = $validated->validated();
        // Update only provided fields
        $people->update([
            'name' => $validateData['name'],
            'phone' => $validateData['phone'],
            'address' => $validateData['address'],
            'project_id' => $validateData['project_id'],
            'case_id' => $validateData['case_id'],
        ]);

        // Load relationships
        $people->load(['project', 'user', 'cases']);

        return response()->json([
            'message' => 'سودمەند بە سەرکەوتوویی نوێکرایەوە',
            'people' => $people
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Delete people
        $people = People::findOrFail($id);

        $people->delete();

        return response()->json(['message' => 'سودمەند بە سەرکەوتوویی سڕایەوە']);
    }


    public function recived(Request $request, $id)
    {
        // Validate input
        $validated = $request->validate([
            'date_received' => 'required|integer|in:0,1'
        ]);
    
        $people = People::findOrFail($id);
        $project = $people->project;
    
        if ($validated['date_received'] == 0) {
            // Reset date_received and restore project balance
            $people->update(['date_received' => null]);
            $project->increment('balance', $people->aid);
    
            $message = 'هەڵوەشاندنەوەی یارمەتی ';
        } elseif ($project->balance >= $people->aid) {
            // Deduct balance and set received date
            $project->decrement('balance', $people->aid);
            $people->update(['date_received' => Carbon::now()]);
    
            $message = 'یارمەتی وەرگیرا';
        } else {
            return response()->json([
                'message' => 'باڵانسی بەردەست نەماوە'
            ], 400);
        }
    
        // Reload relationships and return response
        $people->load(['project', 'user']);
    
        return response()->json([
            'message' => $message,
            'people' => $people
        ]);
    }
    
}
