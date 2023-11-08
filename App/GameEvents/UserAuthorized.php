<?php

namespace App\GameEvents;

use App\User;

/**
 * When a user is logged in
 */
class UserAuthorized implements GameEventInterface
{
    protected User $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getId(): string
    {
        return 'user_authorized';
    }

    public function getData(): array
    {
        return ['login' => $this->user->login];
    }
}
