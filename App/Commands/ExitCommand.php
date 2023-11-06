<?php

namespace App\Commands;

use App\Commands\AbstractCommand;
use App\Game;
use App\User;

class ExitCommand extends AbstractCommand
{
    public User $user;
    
    public function call() 
    {
        $this->user->exit();
    }
    
    public function __construct(Game $game, User $user, array $arguments) 
    {
        parent::__construct($game, $user, $arguments);
        // do nothing
    }
}
