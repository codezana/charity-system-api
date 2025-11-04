<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Project, Donation, Expense, People, Aid, Debt};

class IndexController extends Controller
{
    public function index()
    {
        // Total counts
        $userCount = User::count();
        $projectCount = Project::count();
        $donationCount = Donation::count();
        $expenseCount = Expense::count();
        $peopleCount = People::count();
        $aidCount = Aid::count();
        $debtCount = Debt::count();

        // Financial summaries
        $totalDonations = Donation::sum('amount');
        $totalExpenses = Expense::sum('total');
        $totalBalance = $totalDonations - $totalExpenses;

        // Latest 5 donations and expenses
        $latestDonations = Donation::latest()->take(5)->get();
        $latestExpenses = Expense::latest()->take(5)->get();

        // Group donations by payment method
        $donationsByMethod = Donation::selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'userCount' => $userCount,
            'projectCount' => $projectCount,
            'donationCount' => $donationCount,
            'expenseCount' => $expenseCount,
            'peopleCount' => $peopleCount,
            'aidCount' => $aidCount,
            'debtCount' => $debtCount,
            'totalDonations' => $totalDonations,
            'totalExpenses' => $totalExpenses,
            'totalBalance' => $totalBalance,
            'latestDonations' => $latestDonations,
            'latestExpenses' => $latestExpenses,
            'donationsByMethod' => $donationsByMethod
        ]);
    }
}
