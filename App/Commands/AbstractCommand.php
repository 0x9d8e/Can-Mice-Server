<?php

namespace App\Commands;

use App\Game;
use App\User;

abstract class AbstractCommand 
{
    public User $user;
    public Game $game;
    
    public function __construct(Game $game, User $user, array $arguments)
    {
        $this->game = $game;
        $this->user = $user;
    }

    abstract function call();
}
