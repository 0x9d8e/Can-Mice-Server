<?php

namespace App;

class AuthCommand extends AbstractCommand
{
    public string $login;
    
    public function call() 
    {
        $this->game->addUser($this->login);
    }
}
