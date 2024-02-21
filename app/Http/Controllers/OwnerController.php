<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if login is administrator
        if (Auth::user()->role === 'administrator') {
            $owners = Auth::user()->administrator->condominium->owners;

            // Return Owner name and unit name
            return response()->json($owners->map(function ($owner) {
                // find user owner
                return [
                    'id' => $owner->id,
                    'name' => User::find($owner->user_id)->name,
                    'unit' => Unit::find($owner->unit_id)->unit_number,
                    'email' => User::find($owner->user_id)->email,
                    'status' => $owner->is_verified ? 'Verified' : 'Not Verified',
                    'created_at' => $owner->created_at
                ];
            }), 200);

            // Return owners
            return response()->json($owners ?? [], 200);
        } else {
            return response()->json([
                'message' => 'You are not authorized to view owners',
            ], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Owner $owner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Owner $owner)
    {
        // Get Owner id
        $owner_id = Auth::user()->owner->id;

        // Save unit_id
        $owner->unit_id = $request->unit_id;
        $owner->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Owner $owner)
    {
        //
    }

    public function assignUnit(Request $request)
    {
        // Get Owner id
        $owner_id = Auth::user()->owner->id;

        // Condominium id
        $condominium_id = $request->condominium_id;

        // Unit id
        $unit_id = $request->unit_id;

        // Save unit_id
        $owner = Owner::find($owner_id);
        $owner->unit_id = $unit_id;
        $owner->condominium_id = $condominium_id;
        $owner->save();

        return response()->json([
            'message' => 'Unit assigned successfully',
            'user' => Auth::user(),
        ], 200);
    }

    public function me()
    {
        // Get user data
        $user = Auth::user();

        // Return only id, name, role and owner data
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'owner' => $user->owner->only(['id', 'unit_id', 'is_verified'])
        ];

        return response()->json($data, 200);
    }

    public function approveOwner(Request $request)
    {
        // if login is administrator
        if (Auth::user()->role === 'administrator') {
            $owner = Auth::user()->administrator->condominium->owners->find($request->owner_id);
            $owner->is_verified = true;
            $owner->save();

            return response()->json([
                'message' => 'Owner approved successfully',
                'owner' => $owner,
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not authorized to approve owner',
            ], 401);
        }
    }

    public function unlinkOwner(Request $request)
    {
        // if login is administrator
        if (Auth::user()->role === 'administrator') {
            // Get owner
            $owner = Auth::user()->administrator->condominium->owners->find($request->owner_id);

            // Get unit
            $unit = Auth::user()->administrator->condominium->units->find($owner->unit_id);
            $unit->owner_id = null; // Unlink owner

            $owner->unit_id = null; // Unlink unit
            $owner->condominium_id = null; // Unlink condominium
            $owner->is_verified = false; // Unverify owner
            $owner->save(); // Save owner

            return response()->json([
                'message' => 'Owner unlinked successfully',
                'owner' => $owner,
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not authorized to unlink owner',
            ], 401);
        }
    }
}
