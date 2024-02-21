<?php

namespace App\Http\Controllers;

use App\Models\ReportedPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportedPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Get authenticated user
        $user = Auth::user();

        // Get all reported payments from the authenticated user if user is owner
        if ($user->role === 'owner') {
            $reportedPayments = ReportedPayment::where('owner_id', $user->owner->id)->get();
        } else if ($user->role === 'administrator') {
            // Get all reported payments from the condominium
            $reportedPayments = ReportedPayment::where('condominium_id', $user->condominium->id)->get();
        }

        // Return a JSON response with the reported payments
        return response()->json(
            $reportedPayments,
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Get authenticated user
        $user = Auth::user();

        // Create a new reported payment
        $reportedPayment = ReportedPayment::create([
            'owner_id' => $user->owner->id,
            'amount' => $request->amount,
            'bank' => $request->bank,
            'description' => $request->description,
            'is_verified' => false,
            'date' => $request->date,
        ]);

        // Return a JSON response with the reported payment
        return response()->json([
            'message' => 'Successfully reported payment',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportedPayment $reportedPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReportedPayment $reportedPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportedPayment $reportedPayment)
    {
        //
    }
}
