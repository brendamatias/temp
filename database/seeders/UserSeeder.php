<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Database\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@teste.com',
            'password' => Hash::make('123456'),
        ]);
    }
} 