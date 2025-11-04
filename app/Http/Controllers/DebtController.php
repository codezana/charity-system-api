<?php

namespace App\Http\Controllers;

use App\Models\{Debt, Expense,};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function viewdebt()
    {
        $debtexpense = Expense::where('status', 'unpaid')->get();

        return response()->json($debtexpense);
    }


    public function index()
    {

        $debts = Debt::with('expense.category')->get();
        return response()->json($debts);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_id' => 'required|exists:expenses,id',
            'paid' => 'required|numeric|min:1',
            'due_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        $validated = $validator->validated();
        $paidAmount = $validated['paid'];

        // Fetch related records
        $expense = isset($validated['expense_id']) ? Expense::find($validated['expense_id']) : null;

        // Check if the paid amount exceeds the remaining balance
        if ($expense && $paidAmount > ($expense->total - $expense->paid)) {
            return response()->json([
                'error' => 'ناتوانیت بڕی دانەوەی قەرز زیاتر لە پارەدانی دراو بۆ خەرجی'
            ], 422);
        }


        // Update paid amounts
        if ($expense) {
            $expense->increment('paid', $paidAmount);
            if ($expense->paid >= $expense->total) {
                $expense->update(['status' => 'paid']);
            }
        }

        // Create the debt record
        $debt = Debt::create($validated);

        // Load relationships
        $debt->load('expense.category');

        return response()->json([
            'message' => 'قەرز بە سەرکەوتوویی دروست کرا',
            'debt' => $debt
        ], 201);
    }




    /**
     * Display the specified resource.
     */

    public function show($id)
    {
        $debt = Debt::with('expense.category')->findOrFail($id);

        return response()->json($debt);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'expense_id' => 'nullable|exists:expenses,id',
            'paid' => 'required|numeric|min:1',
            'due_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => collect($validator->errors()->all())->first()
            ], 422);
        }

        $validated = $validator->validated();
        $newPaid = $validated['paid'];

        try {
            // Find the existing debt record
            $debt = Debt::findOrFail($id);
            $oldPaid = $debt->paid;
            $paidDifference = $newPaid - $oldPaid;

            // Fetch related records (only if IDs are provided)
            $expense = $debt->expense_id ? Expense::findOrFail($debt->expense_id) : null;

            // Check if new payment exceeds allowed balance **(only for increasing payments)**
            if ($paidDifference > 0) {
                if ($expense && ($expense->paid + $paidDifference) > $expense->total) {
                    return response()->json(['error' => 'ناتوانیت بڕی دانەوەی قەرز زیاتر بێت لە پارەدانی دراو لە خەرجی'], 422);
                }
            }

            // Adjust paid amounts **(ensuring valid balance update)**
            if ($paidDifference > 0) {
                if ($expense) $expense->increment('paid', $paidDifference);
            } elseif ($paidDifference < 0) {
                if ($expense && $expense->paid >= abs($paidDifference)) {
                    $expense->decrement('paid', abs($paidDifference));
                }
            }

            // Update debt record
            $debt->update($validated);

            // Update status (only for sent fields)
            if ($expense) {
                $expense->update(['status' => $expense->paid >= $expense->total ? 'paid' : 'unpaid']);
            }
    

            // Reload relationships
            $debt->load('expense.category');

            return response()->json([
                'message' => 'قەرز بە سەرکەوتوویی نوێ کرایەوە',
                'debt' => $debt
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'نەتوانرا قەرز نوێ بکرێتەوە'
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        try {
            $debt = Debt::findOrFail($id);

            // Fetch related records safely
            $expense = $debt->expense_id ? Expense::find($debt->expense_id) : null;

            // Subtract paid amount before deleting
            if ($expense) {
                $expense->decrement('paid', $debt->paid);
                $expense->update(['status' => $expense->paid < $expense->total ? 'unpaid' : 'paid']);
            }

            // Delete the debt record
            $debt->delete();

            return response()->json([
                'message' => 'قەرز بە سەرکەوتوویی سڕایەوە'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'نەتوانرا قەرز بسڕدرێتەوە'
            ], 500);
        }
    }
}
