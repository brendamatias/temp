<?php

namespace App\Application\UseCases\Auth;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\AuthService;

class UpdateProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly AuthService $authService
    ) {}

    public function execute(
        int $userId,
        string $name,
        string $email,
        ?string $phone,
        ?string $password
    ): User {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \Exception('Usuário não encontrado');
        }

        $user->setName($name);
        $user->setEmail($email);
        $user->setPhone($phone);

        if ($password) {
            $user->setPassword($this->authService->hashPassword($password));
        }

        return $this->userRepository->update($user);
    }
} 