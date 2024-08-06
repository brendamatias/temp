<?php

namespace App\Application\UseCases\Auth;

use App\Application\DTOs\UserDTO;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\AuthService;

class AuthenticateUserUseCase
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

    public function execute(string $email, string $password): ?UserDTO
    {
        if (!$this->authService->validateCredentials($email, $password)) {
            return null;
        }

        $user = $this->userRepository->findByEmail($email);

        return new UserDTO(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
            emailVerifiedAt: $user->getEmailVerifiedAt()
        );
    }
} 