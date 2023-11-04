<?php

namespace App;

class MoveCommand extends AbstractCommand
{
    const DIRECTION_UP          = b1000; // up
    const DIRECTION_UP_RIGHT    = b1010; // up|right
    const DIRECTION_RIGHT       = b0010; // right
    const DICRECTION_DONW_RIGHT = b0110; // down|right
    const DIRECTION_DOWN        = b0100; // down
    const DIRECTION_DOWN_LEFT   = b0101; // down|left
    const DIRECTION_LEFT        = b0001; // left
    const DIRECTION_UP_LEFT     = b1001; // up|left

    public User $user;
    public int $direction;

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
