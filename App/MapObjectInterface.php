<?php

namespace App;

interface MapObjectInterface 
{
    public function getMapObjectId(): int;
    public function setMapObjectId(int $id): void;

    public function getTypeId(): string;
    public function getTypeSpecificData(): array;

    public function getPosition(): Position;
    public function setPosition(Position $position): void;
    
    public function touch(MapObjectInterface $object, Position $newPosition): void;
    
    public function isWall(): bool;
    public function canBeMouseFood(): bool;
    public function canBeCatFood(): bool;
    public function canFight(): bool;
}
