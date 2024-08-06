<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\Database\Models\User as UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $userModel = UserModel::find($id);
        
        if (!$userModel) {
            return null;
        }

        return $this->toEntity($userModel);
    }

    public function findByEmail(string $email): ?User
    {
        $userModel = UserModel::where('email', $email)->first();
        
        if (!$userModel) {
            return null;
        }

        return $this->toEntity($userModel);
    }

    public function save(User $user): User
    {
        $userModel = new UserModel();
        $userModel->name = $user->getName();
        $userModel->email = $user->getEmail();
        $userModel->phone = $user->getPhone();
        $userModel->password = $user->getPassword();
        $userModel->email_verified_at = $user->getEmailVerifiedAt();
        $userModel->remember_token = $user->getRememberToken();
        $userModel->save();

        $user->setId($userModel->id);
        return $user;
    }

    public function delete(int $id): bool
    {
        return UserModel::destroy($id) > 0;
    }

    public function update(User $user): User
    {
        $userModel = UserModel::find($user->getId());
        
        if (!$userModel) {
            throw new \Exception('User not found');
        }

        $userModel->name = $user->getName();
        $userModel->email = $user->getEmail();
        $userModel->phone = $user->getPhone();
        $userModel->password = $user->getPassword();
        $userModel->email_verified_at = $user->getEmailVerifiedAt();
        $userModel->remember_token = $user->getRememberToken();
        $userModel->save();

        return $user;
    }

    private function toEntity(UserModel $model): User
    {
        $user = new User(
            name: $model->name,
            email: $model->email,
            password: $model->password,
            phone: $model->phone,
            emailVerifiedAt: $model->email_verified_at,
            rememberToken: $model->remember_token
        );
        
        $user->setId($model->id);
        
        return $user;
    }
} 