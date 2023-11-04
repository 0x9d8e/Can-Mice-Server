<?php

namespace App;

interface MapObject 
{
    public function getPosition(): Position;
    public function setPosition(Position $position): void;
    
    public function touch(MapObject $object, Position $newPosition): void;
    
    public function isWall(): bool;
    public function canBeMouseFood(): bool;
    public function canBeCatFood(): bool;
    public function canFight(): bool;
    
}
