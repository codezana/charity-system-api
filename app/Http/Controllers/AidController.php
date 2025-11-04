<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\People;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AidController extends Controller
{

    /**
     * Get people case by project ID
     */
    public function filtterCase(Request $request)
    {
        $cases = People::where('project_id', $request->project_id)
            ->select('id as case_id', 'name as case_name')
            ->get();

        return response()->json($cases);
    }
    



    /**
     * Store a newly created resource in storage.
     */
    public function update(Request $request)
    {
        // Validate input
        $validated = Validator::make($request->all(), [
            'case_id' => 'required|numeric|exists:cases,id',
            'amount' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'error' => collect($validated->errors()->all())->first()
            ], 422);
        }

        $validateData = $validated->validated();
        $caseId = $validateData['case_id'];
        $amount = $validateData['amount'];
 
        // Retrieve people with the specified case ID and update their aid field
        $people = People::where('case_id', $caseId)->get();
        foreach ($people as $person) {
            $person->update(['aid' => $amount]);
        }

        return response()->json([
            'message' => 'بڕی یارمەتی بە سەرکەوتوویی نوێکرانەوە بۆ هەموو کەسانی پەیوەندیدار',
        ], 201);
    }


}
