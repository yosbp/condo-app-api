<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Income;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all incomes associated with logged in user and condominium
        $incomes = Income::where('condominium_id', Auth::user()->condominium->id)->get();

        // Added unit number from unit_id to the incomes
        foreach ($incomes as $income) {
            $income->unit_number = Unit::find($income->unit_id)->unit_number;
        }

        // Delete the condominium_id and unit_id from the incomes
        $incomes->makeHidden(['condominium_id', 'unit_id']);

        // Return a JSON response with the incomes
        return response()->json(
            $incomes,
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
            // Create a new income
            $income = Income::create([
                'description' => $request->description,
                'amount' => $request->amount,
                'condominium_id' => $condominium_id,
                'unit_id' => $request->unit_id,
                'date' => date('Y-m-d', strtotime($request->date)),
            ]);

            // Get previous balance and add the new with the new income
            $balance = Balance::where('condominium_id', $condominium_id)->latest()->first();
            $new_balance = $balance->balance + $request->amount;

            // Create a new balance
            $balance = Balance::create([
                'condominium_id' => $condominium_id,
                'income_id' => $income->id,
                'balance' => $new_balance,
            ]);

            // Update unit balance
            $unit = Unit::find($request->unit_id);
            $unit->balance = $unit->balance + $request->amount;
            $unit->save();



            // Return a JSON response with the condominium
            return response()->json([
                'message' => 'Successfully created income',
                'income' => $income,
            ], 201);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to create an income',
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Income $income)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Income $income)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        //
    }
}
