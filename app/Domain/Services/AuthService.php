<?php

namespace App\Domain\Services;

use App\Domain\Entities\User;
use App\Infrastructure\Database\Models\User as UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
        $user = UserModel::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at
            ],
            'token' => $token
        ];
    }

    public function login(array $data): array
    {
        $user = UserModel::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Credenciais inválidas');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at
            ],
            'token' => $token
        ];
    }

    public function createUser(array $data): User
    {
        $userModel = UserModel::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return $this->toEntity($userModel);
    }

    public function getCurrentUser(): ?User
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        return $this->toEntity($user);
    }

    private function toEntity(UserModel $model): User
    {
        return new User(
            $model->name,
            $model->email,
            $model->password,
            $model->email_verified_at,
            $model->remember_token
        );
    }
} 