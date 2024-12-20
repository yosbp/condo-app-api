<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all last 5 incomes and expenses from the condominium
        $incomes = Income::where('condominium_id', Auth::user()->condominium->id)->orderBy('date', 'desc')->take(5)->get();
        $expenses = Expense::where('condominium_id', Auth::user()->condominium->id)->orderBy('date', 'desc')->take(5)->get();

        // Mixed incomes and expenses ordered by asc
        $transactions = $incomes->merge($expenses)->sortBy(function ($transaction) {
            return [$transaction->date, $transaction instanceof Income ? 1 : 0];
        }, SORT_REGULAR)->values();


        $transactions = $transactions->map(function ($item, $key) {
            $item->makeHidden(['condominium_id', 'unit_id']);
            $item->type = $item instanceof Income ? 'income' : 'expense';
            $item->number = str_pad($key + 1, 2, '0', STR_PAD_LEFT);
            return $item;
        });

        // Get condominium balance
        $balance = Auth::user()->condominium->balances->last()->balance;

        // Get total units
        $total_units = Auth::user()->condominium->units->count();

        // Get total monthly income and expenses
        $total_monthly_income = Income::where('condominium_id', Auth::user()->condominium->id)->whereMonth('date', date('m'))->sum('amount');
        $total_monthly_expense = Expense::where('condominium_id', Auth::user()->condominium->id)->whereMonth('date', date('m'))->sum('amount');
        $difference = $total_monthly_income - $total_monthly_expense;
        $last_day_of_actual_month = date('t/m/Y');

        //Get total previous monthly income and expenses
        $total_previous_monthly_income = Income::where('condominium_id', Auth::user()->condominium->id)->whereMonth('date', date('m', strtotime('-1 month')))->sum('amount');
        $total_previous_monthly_expense = Expense::where('condominium_id', Auth::user()->condominium->id)->whereMonth('date', date('m', strtotime('-1 month')))->sum('amount');
        $previous_difference = $total_previous_monthly_income - $total_previous_monthly_expense;
        $last_day_of_previous_month = date('t/m/Y', strtotime('-1 month'));


        // Get the balances for the last 15 days, sorted by created_at in ascending order
        $balances = Auth::user()->condominium->balances
            ->where('created_at', '>=', now()->subDays(15))
            ->sortBy('created_at');

        // Initialize an empty collection for the result
        $evolution_balance = collect();

        // Set the start date to 14 days ago (to include today)
        $startDate = now()->subDays(14);  // 14 to include the current day
        $previousBalance = null; // To store the balance from the previous day

        for ($date = $startDate; $date->lte(now()); $date->addDay()) {
            // Filter balances for the current day and take the last one (most recent)
            $balanceForDay = $balances->filter(function ($balance) use ($date) {
                return $balance->created_at->isSameDay($date);
            })->last(); // Take the last balance of the day, i.e., the most recent

            if ($balanceForDay) {
                // If a balance exists, use it and update the previous balance
                $previousBalance = $balanceForDay->balance;
            }

            // Add the entry with the date and the balance (either the current or previous day's balance)
            $evolution_balance->push([
                'label' => $date->format('d/m'),
                'data'  => $previousBalance,
            ]);
        }

        // get evolution balance only labels and reverse results and get array
        $evolution_balance_labels = $evolution_balance->pluck('label');

        // get evolution balance only data
        $evolution_balance_data = $evolution_balance->pluck('data');


        // Return a JSON response with the incomes and expenses
        return response()->json([
            'transactions' => $transactions,
            'total_units' => $total_units,
            'balance' => $balance,
            'month_balance' => [
                'month' => date('F'), // 'F' returns the month name, e.g. 'February
                'total_monthly_income' => $total_monthly_income,
                'total_monthly_expense' => $total_monthly_expense,
                'difference' => $difference,
                'last_day_of_month' => $last_day_of_actual_month,
            ],
            'previous_month_balance' => [
                'month' => date('F', strtotime('-1 month')),
                'total_monthly_income' => $total_previous_monthly_income,
                'total_monthly_expense' => $total_previous_monthly_expense,
                'difference' => $previous_difference,
                'last_day_of_month' => $last_day_of_previous_month,
            ],
            'evolution_balance' => [
                'labels' => $evolution_balance_labels,
                'data' => $evolution_balance_data
            ]
        ], 200);
    }
}
