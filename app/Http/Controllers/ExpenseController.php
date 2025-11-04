<?php

namespace App\Http\Controllers;

use App\Models\{Expense, Project, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Display expense
        $expense = Expense::with('project', 'user', 'category')->get();
        return response()->json($expense);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate and create Expense:
        $validator = Validator::make($request->all(), ([
            'project_id' => 'required|numeric|exists:projects,id',
            'name' => 'required|string',
            'category' => 'nullable|numeric|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'total' => 'required|numeric',
            'payment_method' => 'required|in:FIB,Fastpay,Cash,Other',
            'paid' => 'required|numeric',
            'invoice' => 'nullable|string'
        ]));

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        $validateData = $validator->validated();

        // Create the Expense:
        $expense = Expense::create([
            'project_id' => $validateData['project_id'],
            'made' => auth()->user()->id,
            'name' => $validateData['name'],
            'category' => $validateData['category'],
            'description' => $validateData['description'],
            'price' => $validateData['price'],
            'quantity' => $validateData['quantity'],
            'total' => $validateData['total'],
            'payment_method' => $validateData['payment_method'],
            'paid' => $validateData['paid'],
            'invoice' => $validateData['invoice'],
            'status' => (float)$validateData['total'] === (float)$validateData['paid'] ? 'paid' : 'unpaid',
        ]);

        //Retrive total_expense from project
        $project = Project::find($expense->project_id);
        $project->total_expenses += $expense->total;
        $project->balance -= $expense->total;
        $project->save();

        // Load relationships
        $expense->load(['user', 'project', 'category']);

        return response()->json([
            'message' => 'خەرجی بە سەرکەوتوویی دروستکرا',
            'expense' => $expense
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Show specific Expense:
        $expense = Expense::with('user', 'project', 'category')->findOrFail($id);
        return response()->json($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the expense
        $expense = Expense::findOrFail($id);

        // Store the original expense amount before updating
        $originalAmount = $expense->total;

        // Validate input
        $validator = Validator::make($request->all(), [
            'project_id' => 'numeric|exists:projects,id',
            'name' => 'string',
            'category' => 'nullable|numeric|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'numeric',
            'quantity' => 'integer',
            'total' => 'numeric',
            'payment_method' => 'in:FIB,Fastpay,Cash,Other',
            'paid' => 'numeric',
            'invoice' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        $validatedData = $validator->validated();

        // Calculate the difference
        $difference = $validatedData['total'] - $originalAmount;

        // Update the expense
        $expense->update([
            'project_id' => $validatedData['project_id'],
            'name' => $validatedData['name'],
            'category' => $validatedData['category'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'quantity' => $validatedData['quantity'],
            'total' => $validatedData['total'],
            'payment_method' => $validatedData['payment_method'],
            'paid' => $validatedData['paid'],
            'invoice' => $validatedData['invoice'],
            'status' => (float)$validatedData['total'] === (float)$validatedData['paid'] ? 'paid' : 'unpaid',
        ]);

        // Update total_expenses in the project safely
        $project = Project::find($expense->project_id);
        if ($project) {
            if ($difference > 0) {
                $project->increment('total_expenses', $difference);
                $project->decrement('balance', $difference);
            } elseif ($difference < 0) {
                $project->decrement('total_expenses', abs($difference));
                $project->increment('balance', abs($difference));
            }
        }

        // Load relationships
        $expense->load(['user', 'project', 'category']);

        return response()->json([
            'message' => 'خەرجی بە سەرکەوتوویی نوێکرایەوە',
            'expense' => $expense
        ]);
    }



    public function destroy($id)
    {
        // Find the expense
        $expense = Expense::findOrFail($id);

        // Store the original expense amount before deleting
        $originalAmount = $expense->total;

        // Get the associated project
        $project = Project::find($expense->project_id);

        // Delete the expense
        $expense->delete();

        // Update total_expenses in the project safely
        if ($project) {
            $project->decrement('total_expenses', $originalAmount);
            $project->increment('balance', $originalAmount);
        }

        return response()->json(['message' => 'خەرجی بە سەرکەوتوویی سڕایەوە']);
    }
}
