<?php

namespace App;

class ExitCommand extends AbstractCommand
{
    public User $user;
    
    public function call() 
    {
        $this->user->exit();
    }
}
