<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Display projects
        $projects = Project::with('user')->get();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate and create project
        $validator = Validator::make($request->all(), ([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_date' => 'required|date',
            'goal_amount' => 'required|numeric',
            'end_date' => 'required|date'
        ]));

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        $validateData = $validator->validated();

        // Create the project
        $project = Project::create([
            'made' => auth()->user()->id,
            'title' => $validateData['title'],
            'description' => $validateData['description'],
            'location' => $validateData['location'],
            'start_date' => $validateData['start_date'],
            'goal_amount' => $validateData['goal_amount'],
            'end_date' => $validateData['end_date']
        ]);

        // Load relationships
        $project->load(['user']);

        return response()->json([
            'message' => 'پڕۆژە بە سەرکەوتوویی دروستکرا',
            'project' => $project
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Show specific project
        $project = Project::with('user')->findOrFail($id);
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        // Validate input
        $validated = Validator::make($request->all(), ([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_date' => 'required|date',
            'goal_amount' => 'required|numeric',
            'end_date' => 'required|date',
        ]));

        if ($validated->fails()) {
            return response()->json([
                'error' => collect($validated->errors()->all())->first()
            ], 422);
        }
        // Find the expense
        $project = Project::findOrFail($id);

        $validateData = $validated->validated();
        // Update only provided fields
        $project->update([
            'title' => $validateData['title'],
            'description' => $validateData['description'],
            'location' => $validateData['location'],
            'start_date' => $validateData['start_date'],
            'goal_amount' => $validateData['goal_amount'],
            'end_date' => $validateData['end_date'],
        ]);

        // Load relationships
        $project->load(['user']);

        return response()->json([
            'message' => 'پڕۆژە بە سەرکەوتوویی نوێکرایەوە',
            'project' => $project
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Delete project
        $project = Project::findOrFail($id);

        // Check if project is used by donation, expense or beneficiary
        if ($project->donations()->exists() || $project->expenses()->exists() || $project->people()->exists()) {
            return response()->json([
                'error' => 'پڕۆژە لەلایەن سودمەندان و خەرجی و بەخشەرەکان بەکاردەهێنرێت و ناتوانێت بیسڕیتەوە'
            ], 422);
        }

        $project->delete();

        return response()->json(['message' => 'پڕۆژە بە سەرکەوتوویی سڕایەوە']);
    }
}
