<?php

namespace App\GameEvents;

use App\User;

/**
 * When a user has logged out of the game
 */
class UserExit implements GameEventInterface
{
    protected User $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getId(): string
    {
        return 'user_exit';
    }

    public function getData(): string
    {
        return ['login' => $this->user->login];
    }
}
