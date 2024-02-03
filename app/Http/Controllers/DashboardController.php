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
      

        // Get evolution balance in last 30 days in array form, to be used in the chart, can get label dates and balance values like label: [date1, date2, date3], data: [balance1, balance2, balance3] in desc order
        $evolution_balance = Auth::user()->condominium->balances->where('created_at', '>=', now()->subDays(30))->map(function ($item) {
            return [
                'label' => $item->created_at->format('d/m'),
                'data' => $item->balance
            ];
        });

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
