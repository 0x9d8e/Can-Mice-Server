<?php

namespace App\Commands;

use App\Game;
use App\User;
use Exception;

class CommandFabric 
{
    public User $user;
    public Game $game;
    
    public function __construct(Game $game, User $user)
    {
        $this->game = $game;
        $this->user = $user;
    }
    
    public function make(string $message): AbstractCommand
    {
        $data = json_decode($message, true);
        $command = $data['command'];
        $arguments = $data['arguments'] ?? [];
        switch ($data['command']) {
            case 'auth':
                $command = new AuthCommand($this->game, $this->user, $arguments);
                break;
            case 'move':
                $command = new MoveCommand($this->game, $this->user, $arguments);
                break;
            case 'exit':
                $command = new ExitCommand($this->game, $this->user, $arguments);
                break;
            default: 
                throw new Exception("Unexpected command '$command'!");
        }
        
        return $command;
    }
}
