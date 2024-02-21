<?php

namespace App\Http\Controllers;

use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get authenticated user
        $user = Auth::user();

        // Get all unit types associated with logged in user and condominium
        $unitTypes = UnitType::where('condominium_id', $user->condominium->id)->get();

        // Return a JSON response with the unit types
        return response()->json(
            $unitTypes,
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
            // Create a new unit type
            $unitType = UnitType::create([
                'condominium_id' => $condominium_id,
                'name' => $request->name,
                'description' => $request->description,
                'percentage' => $request->percentage,
            ]);

            // Return a JSON response with the unit type
            return response()->json([
                'message' => 'Successfully created unit type',
                'unitType' => $unitType,
            ], 201);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to create a unit type',
            ], 403);
        }        
    }

    /**
     * Display the specified resource.
     */
    public function show(UnitType $unitType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnitType $unitType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitType $unitType)
    {
        //
    }
}
