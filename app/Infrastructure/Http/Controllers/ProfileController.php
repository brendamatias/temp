<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\UseCases\Auth\UpdateProfileUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UpdateProfileUseCase $updateProfileUseCase
    ) {}

    public function show(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'current_password' => 'required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $user = $this->updateProfileUseCase->execute(
                auth()->id(),
                $validated['name'],
                $validated['email'],
                $validated['phone'] ?? null,
                $validated['password'] ?? null
            );

            return response()->json([
                'message' => 'Perfil atualizado com sucesso',
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'phone' => $user->getPhone(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 