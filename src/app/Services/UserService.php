<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUserByEmail(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }
}
