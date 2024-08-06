<?php

namespace App\Infrastructure\Services;

use App\Domain\Entities\User;
use App\Infrastructure\Database\Models\User as UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function validateCredentials(string $email, string $password): bool
    {
        return Auth::attempt(['email' => $email, 'password' => $password]);
    }

    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function createUser(array $data): User
    {
        $userModel = UserModel::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->hashPassword($data['password']),
        ]);

        return new User(
            id: $userModel->id,
            name: $userModel->name,
            email: $userModel->email,
            password: $userModel->password
        );
    }

    public function getCurrentUser(): ?User
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return new User(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            password: $user->password
        );
    }
} 