<?php

namespace App\Http\Controllers;

use App\Models\{Project, Donation, Expense, Beneficiary, AidDistribution};

class ReportController extends Controller
{
    /**
     * Get summary report for all projects.
     */
    public function projectSummary()
    {
        $projects = Project::with(['donations', 'expenses', 'beneficiaries'])->get();
        
        $summary = $projects->map(function ($project) {
            return [
                'title' => $project->title,
                'goal_amount' => $project->goal_amount,
                'total_donations' => $project->total_donations,
                'total_expenses' => $project->total_expenses,
                'balance' => $project->balance,
                'status' => $project->status,
            ];
        });
        
        return response()->json($summary);
    }

    /**
     * Get donation report.
     */
    public function donationReport()
    {
        $donations = Donation::with('project')->get();
        
        return response()->json($donations);
    }

    /**
     * Get expense report.
     */
    public function expenseReport()
    {
        $expenses = Expense::with(['project', 'category'])->get();
        
        return response()->json($expenses);
    }

    /**
     * Get aid distribution report.
     */
    public function aidDistributionReport()
    {
        $aidDistributions = AidDistribution::with(['beneficiary', 'beneficiary.project'])->get();
        
        return response()->json($aidDistributions);
    }
}
