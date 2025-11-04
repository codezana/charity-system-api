<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Donation, Project};
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Display Donation
        $donation= Donation::with('project', 'user')->get();
        return response()->json($donation);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate and create Donation:
        $validator = Validator::make($request->all(), ([
            'project_id' => 'required|numeric|exists:projects,id',
            'name' => 'nullable|string',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:FIB,Fastpay,Cash,Other',
        ]));

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        $validateData = $validator->validated();

        // Create the Donation:
        $donation= Donation::create([
            'project_id' => $validateData['project_id'],
            'made' => auth()->user()->id,
            'name' => $validateData['name'],
            'amount' => $validateData['amount'],
            'payment_method' => $validateData['payment_method'],
        ]);

        //Retrive total_donation from project
        $project = Project::find($donation->project_id);
        $project->total_donations += $donation->amount;
        $project->balance += $donation->amount;
        $project->save();

        // Load relationships
        $donation->load(['user', 'project']);

        return response()->json([
            'message' => 'هاوکاری بە سەرکەوتوویی دروستکرا',
            'donation' => $donation
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Show specific Donation:
        $donation= Donation::with('user', 'project')->findOrFail($id);
        return response()->json($donation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the donation
        $donation = Donation::findOrFail($id);
    
        // Store the original donation amount before updating
        $originalAmount = $donation->amount;
    
        // Validate input
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|numeric|exists:projects,id',
            'name' => 'nullable|string',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:FIB,Fastpay,Cash,Other',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }
    
        $validatedData = $validator->validated();
    
        // Calculate the difference
        $difference = $validatedData['amount'] - $originalAmount;
    
        // Update the donation
        $donation->update([
            'project_id' => $validatedData['project_id'],
            'name' => $validatedData['name'],
            'amount' => $validatedData['amount'],
            'payment_method' => $validatedData['payment_method'],
        ]);
    
        // Update total_donations in the project safely
        $project = Project::find($donation->project_id);
        if ($project) {
            if ($difference > 0) {
                $project->increment('total_donations', $difference);
                $project->increment('balance', $difference);
            } elseif ($difference < 0) {
                $project->decrement('total_donations', abs($difference));
                $project->decrement('balance', abs($difference));
            }
        }
    
        // Load relationships
        $donation->load(['user', 'project']);
    
        return response()->json([
            'message' => 'هاوکاری بە سەرکەوتوویی نوێکرایەوە',
            'donation' => $donation
        ]);
    }
    

    
    public function destroy($id)
    {
        // Find the donation
        $donation = Donation::findOrFail($id);
    
        // Store the original donation amount before deleting
        $originalAmount = $donation->amount;
    
        // Get the associated project
        $project = Project::find($donation->project_id);
    
        // Delete the donation
        $donation->delete();
    
        // Update total_donations in the project safely
        if ($project) {
            $project->decrement('total_donations', $originalAmount);
            $project->decrement('balance', $originalAmount);
        }
    
        return response()->json(['message' => 'هاوکاری بە سەرکەوتوویی سڕایەوە']);
    }
    
}
