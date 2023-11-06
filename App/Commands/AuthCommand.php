<?php

namespace App\Commands;

use App\Game;
use App\User;
use Exception;

class AuthCommand extends AbstractCommand
{
    public string $login;
    
    public function call() 
    {
        // todo: throw exception on add existed login
        $this->user->auth($this->login);
    }

    public function __construct(Game $game, User $user, array $arguments) 
    {
        $this->login = $arguments['login'] ?? throw new Exception('Bad command! AuthCommand login required.');
        parent::__construct($game, $user, $arguments);
    }
}
