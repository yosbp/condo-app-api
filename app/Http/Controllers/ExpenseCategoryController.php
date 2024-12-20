<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get authenticated user
        $user = Auth::user();

        // Get all expense categories associated with logged in user and condominium
        $expenseCategories = ExpenseCategory::where('condominium_id', $user->condominium->id)->get();

        // Return a JSON response with the expense categories
        return response()->json(
            $expenseCategories,
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
            // Create a new expense category
            $expenseCategory = ExpenseCategory::create([
                'condominium_id' => $condominium_id,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Return a JSON response with the expense category
            return response()->json([
                'message' => 'Successfully created expense category',
                'expenseCategory' => $expenseCategory,
            ], 201);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to create an expense category',
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        // Verify if user id is role administrator
        if (Auth::user()->role === 'administrator') {
            // Update the expense category
            $expenseCategory->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Return a JSON response with the expense category
            return response()->json([
                'message' => 'Successfully updated expense category',
                'expenseCategory' => $expenseCategory,
            ], 200);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to update an expense category',
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        //
    }
}
