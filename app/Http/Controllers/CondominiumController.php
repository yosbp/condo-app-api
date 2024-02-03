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
        //
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
}
