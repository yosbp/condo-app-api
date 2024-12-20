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
        // Get the authenticated user ID
        $user_id = Auth::id();

        // Verify if user logged is related to the condominium
        if ($condominium->administrator->user_id === $user_id) {
            // Update the condominium
            $condominium->update([
                'name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $condominium->country,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'name_to_invoice' => $request->name_to_invoice,
            ]);

            // Return a JSON response with the condominium
            return response()->json([
                'message' => 'Successfully updated condominium',
                'condominium' => $condominium,
            ], 200);
        } else {
            // Return a JSON response with the error message
            return response()->json([
                'message' => 'You are not authorized to update this condominium',
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Condominium $condominium)
    {
        //
    }

    public function uploadImage(Request $request, $id)
    {
        // Get the authenticated user ID
        $user_id = Auth::id();

        // Get the condominium
        $condominium = Condominium::where('id', $id)->first();

        // Verify if user logged is related to the condominium
        if ($condominium->administrator->user_id === $user_id) {
            // Obtener el Base64 desde la petición
            $base64Image = $request->input('image');

            // Verifica si contiene el prefijo 'data:image/...;base64,'
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1); // Eliminar el prefijo
                $fileExtension = strtolower($type[1]); // Obtener la extensión (png, jpg, etc.)

                // Decodificar el Base64
                $image = base64_decode($base64Image);

                // Verificar si la decodificación fue exitosa
                if ($image === false) {
                    return response()->json(['message' => 'Base64 decoding failed'], 400);
                }

                // Generar un nombre de archivo único con la extensión correcta
                $fileName = uniqid('logo_') . '.' . $fileExtension;

                // Definir la ruta de almacenamiento
                $filePath = public_path('logos') . '/' . $fileName;

                // Guardar la imagen
                file_put_contents($filePath, $image);

                // Actualizar el URL en el modelo
                $condominium->update([
                    'image_url' => asset('logos/' . $fileName),
                ]);

                return response()->json(['message' => 'Successfully uploaded image', 'condominium' => $condominium], 200);
            } else {
                return response()->json(['message' => 'Invalid image format'], 400);
            }
        } else {
            // Return an unauthorized error response
            return response()->json([
                'message' => 'You are not authorized to upload an image for this condominium',
            ], 403);
        }
    }
}
