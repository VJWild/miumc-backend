<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::listAllWithRelationships());    
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            "full_name" => "sometimes|string|max:255",
            "email" => "sometimes|email|unique:users,email",
            "role" => "sometimes|string|max:255",
            "career_id" => "sometimes|integer|exists:careers,id",
            "specialization_id" => "sometimes|integer|exists:specializations,id"
        ]);

        $user->updateOrFail($validated);
        
        return response()->json([
            "success" => true,
            "message" => "Usuario actualizado correctamente"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "Usuario eliminado correctamente"
        ]);
    }
}
