<?php

namespace App\Application\UseCases\Auth;

use App\Application\DTOs\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\AuthService;

class RegisterUserUseCase
{
    private UserRepositoryInterface $userRepository;
    private AuthService $authService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AuthService $authService
    ) {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    public function execute(string $name, string $email, string $password): UserDTO
    {
        $user = new User(
            name: $name,
            email: $email,
            password: $this->authService->hashPassword($password)
        );

        $savedUser = $this->userRepository->save($user);

        return new UserDTO(
            id: $savedUser->getId(),
            name: $savedUser->getName(),
            email: $savedUser->getEmail(),
            emailVerifiedAt: $savedUser->getEmailVerifiedAt()
        );
    }
} 