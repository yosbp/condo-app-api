<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get authenticated user
        $user = Auth::user();

        // Get all units associated with logged in user and condominium
        $units = Unit::where('condominium_id', $user->condominium->id)->get();

        // Return a JSON response with the condominiums
        return response()->json(
            $units,
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
        if (Auth::user()->role === 'administrator') {
            // Create a new unit
            $unit = Unit::create([
                'unit_number' => $request->unit_number,
                'condominium_id' => $condominium_id,
                'owner_name' => $request->owner_name,
                'balance' => $request->balance,
                'type' => $request->type,
            ]);

            // Return a JSON response with the condominium
            return response()->json([
                'message' => 'Successfully created unit',
                'unit' => $unit,
            ], 201);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to create a unit',
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        //  Get the authenticated user ID
        $user_id = Auth::id();

        // Verify if user logged is related to the condominium
        if ($unit->condominium->administrator->user_id === $user_id) {
            // Update the unit
            $unit->update([
                'owner_name' => $request->owner_name,
            ]);

            // Return a JSON response with the condominium
            return response()->json([
                'message' => 'Successfully updated unit',
            ], 200);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to update this unit',
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        //
    }
}
