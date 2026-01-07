<?php

namespace App\Storage\Implementations;

use App\Models\User;
use App\Storage\UserRepository;
use DateTimeInterface;
use Carbon\Carbon;
use App\Models\RefreshToken;

class DbUserRepository implements UserRepository{
    public function add(User $user){
        $user->save();
    }

     public function getUserByEmail(string $email):?User {
        return User::where('email', $email)->first();
    }

    public function saveRefreshToken(string $user_id, string $refreshToken, DateTimeInterface $expiresAt = null):void{
        $expiresAt = $expiresAt ?? Carbon::now()->addDays(30);

        RefreshToken::create([
            'user_id' => $user_id,
            'token_hash' => $refreshToken,
            'expires' => $expiresAt
        ]);
    }
}


