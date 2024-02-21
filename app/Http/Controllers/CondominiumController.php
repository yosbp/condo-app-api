<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Condominium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CondominiumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the condominiums
        $condominiums = Condominium::all();

        // Get units associated with the condominium
        foreach ($condominiums as $condominium) {
            $condominium->units;
        }

        // Only show condominium name and id and unit name and id
        $condominiums = $condominiums->map(function ($condominium) {
            return [
                'id' => $condominium->id,
                'name' => $condominium->name,
                'units' => $condominium->units->map(function ($unit) {
                    return [
                        'id' => $unit->id,
                        'name' => $unit->unit_number,
                    ];
                }),
            ];
        });

        // Return a JSON response with the condominiums
        return response()->json(
            $condominiums,
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Get Administrator id
        $administrator_id = Auth::user()->administrator->id;

        // Verify if user id is role administrator
        if (Auth::user()->role === 'administrator') {
            // Create a new condominium
            $condominium = Condominium::create([
                'name' => $request->name,
                'administrator_id' => $administrator_id,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'name_to_invoice' => $request->name_to_invoice,
            ]);

            // Create a new balance
            $balance = Balance::create([
                'condominium_id' => $condominium->id,
                'balance' => $request->initial_balance ? $request->initial_balance : 0,
            ]);

            // Return a JSON response with the condominium
            return response()->json([
                'message' => 'Successfully created condominium',
                'condominium' => $condominium,
            ], 201);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to create a condominium',
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Condominium $condominium)
    {
        // Get the authenticated user ID
        $user_id = Auth::id();

        // Verify if user logged is related to the condominium
        if ($condominium->administrator->user_id === $user_id) {
            // Get the condominium
            $condominium = Condominium::where('id', $condominium->id)->first();

            // Return a JSON response with the condominium
            return response()->json([
                'message' => 'Successfully retrieved condominium',
                'condominium' => $condominium,
            ], 200);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to view this condominium',
            ], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Condominium $condominium)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Condominium $condominium)
    {
        //
    }

    public function dataToInvoice()
    {
        // Verify if user id is role administrator
        if (Auth::user()->role === 'administrator') {
            // Get the condominium
            $condominium = Auth::user()->administrator->condominium;

            // Expenses from last 40 days
            $expenses = $condominium->expenses()
                ->where('created_at', '>=', now()->subDays(40))
                ->get()->map(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'name' => $expense->description . ' - ' . date('d/m/Y', strtotime($expense->created_at)) . ' - ' . $expense->amount . '$',
                        'description' => $expense->description,
                        'amount' => $expense->amount,
                        'created_at' => $expense->created_at,
                        'category' => $expense->category->name,
                    ];
                });

            // Prepare data to Only show condominium name_to_invoice, phone and email
            $condominium = [
                'id' => $condominium->id,
                'name_to_invoice' => $condominium->name_to_invoice,
                'phone' => $condominium->phone,
                'email' => Auth::user()->email,
            ];

            // Return a JSON response with the condominiums
            return response()->json(
                [
                    'condominium' => $condominium,
                    'expenses' => $expenses,
                ],
                200
            );
        } else {
            return response()->json([
                'message' => 'You are not authorized to view condominiums',
            ], 401);
        }
    }
}
