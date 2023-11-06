<?php

namespace App\Commands;

use App\Game;
use App\User;
use Exception;

class MoveCommand extends AbstractCommand
{
    const DIRECTION_UP          = b1000; // up
    const DIRECTION_RIGHT       = b0010; // right
    const DIRECTION_DOWN        = b0100; // down
    const DIRECTION_LEFT        = b0001; // left

    public int $direction;
    
    public function __construct(Game $game, User $user, array $arguments) 
    {
        $this->direction = $arguments['direction'] ?? throw new Exception('Bad command! AuthCommand login required.');
        
        if (($this->direction > b1111) ) {
            throw new Exception('Bad command! Unexpected direction ' . $this->direction);
        }
        
        parent::__construct($game, $user, $arguments);
    }

    public function call() 
    {
        $newPosition = clone $this->user->position;
        
        if ($this->direction & self::DIRECTION_UP) {
            $newPosition->up();
        }
        if ($this->direction & self::DIRECTION_DOWN) {
            $newPosition->down();
        }
        if ($this->direction & self::DIRECTION_LEFT) {
            $newPosition->left();
        }
        if ($this->direction & self::DIRECTION_RIGHT) {
            $newPosition->right();
        }
        
        // can't move (out of map)
        if (!$this->game->map->isPositionValid($newPosition)) {
            return;
        }
        
        // can move
        if ($this->game->map->isPositionFree($newPosition)) {
            $this->user->setPosition($newPosition);
        }
        
        // has object
        $object = $this->game->map->get($newPosition);
        
        // TODO: need "transactional" touch
        $userClone = clone $this->user;
        $objectClone = clone $object;
        $this->user->touch($objectClone, $newPosition);
        $object->touch($userClone, $newPosition);
    }
}
