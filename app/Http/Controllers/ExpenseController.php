<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all expenses associated with logged in user and condominium
        $expenses = Expense::where('condominium_id', Auth::user()->condominium->id)->get();

        // Return a JSON response with the expenses
        return response()->json(
            $expenses,
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Get Condominium id
        $condominium_id = Auth::user()->condominium->id;

        // Verify if user id is role administrator
        if (Auth::user()->role === 'administrator' && $condominium_id) {
            // Create a new expense
            $expense = Expense::create([
                'description' => $request->description,
                'amount' => $request->amount,
                'condominium_id' => $condominium_id,
                //format date from 2024-02-01T04:13:09.940Z to functional date to mysql
                'date' => date('Y-m-d', strtotime($request->date)),
            ]);

            // Get previous balance and add the new with the new expense
            $balance = Balance::where('condominium_id', $condominium_id)->latest()->first();
            $new_balance = $balance->balance - $request->amount;

            // Create a new balance
            $balance = Balance::create([
                'condominium_id' => $condominium_id,
                'expense_id' => $expense->id,
                'balance' => $new_balance,
            ]);

            // Return a JSON response with the condominium
            return response()->json([
                'message' => 'Successfully created expense',
                'expense' => $expense,
            ], 201);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to create an expense',
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        //
    }
}
