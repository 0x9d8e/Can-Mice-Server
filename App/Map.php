<?php

namespace App;

use App\GameEvents\GameEventHandler;
use App\GameEvents\NewObjectOnMap;
use Exception;

class Map 
{
    public Game $game;
    public int $width;
    public int $height;
    
    public array $cells = [];
    protected array $objects = [];

    protected int $last_object_id = 1;

    public function __construct(Game $game, int $width, int $height) 
    {
        $this->game = $game;
        $this->width = $width;
        $this->height = $height;
        
        $this->fillEmptyCells();
    }
    
    public function add(MapObjectInterface $object): void
    {
        $position = $object->getPosition();
        if (! $position->isNull()) {
            if (!$this->isCellFree($position->x, $position->y)) {
                throw new Exception("Can not add object to position {$position}. Position is not empty!");
            }
            
            $this->cells[$position->x][$position->y] = $object;
        }
        
        $object->setMapObjectId(++$this->last_object_id);
        
        $this->objects[$object->getMapObjectId()] = $object;
        
        (new GameEventHandler($this->game))->handle(new NewObjectOnMap($object));
    }
    
    public function get(Position $position): MapObjectInterface
    {
        return $this->cells[$position->x][$position->y] ?? throw new Exception("Has not object at {$position}");
    }
    
    /**
     * 
     * @return MapObjectInterface[]
     */
    public function getObjects(): array
    {
        return array_values($this->objects);
    }

    public function freePosition(Position $position): void
    {
        if ($position->isNull()) {
            return;
        }
        
        if (empty($this->cells[$position->x][$position->y])) {
            return;
        }
        
        $object = $this->cells[$position->x][$position->y];
        $object->setPosition(Position::makeNull());
       
        $this->cells[$position->x][$position->y] = null;
        
        unset($this->objects[$object->getMapObjectId()]);
    }
    
    public function getFreePosition(): Position
    {
        for($x = 1; $x < $this->width - 1; $x++) {
            for($y = 1; $y < $this->height -1; $y++) {
                if ($this->isSquare3x3Free($x, $y)) {
                    return new Position($x, $y);
                }
            }
        }
        
        throw new Exception('Can not find free position on map!');
    }
    
    public function isPositionValid(Position $position): bool 
    {
        if ($position->isNull()) {
            return false;
        }
        
        return $this->isCoordinatesCorrect($position->x, $position->y);
    }


    public function isPositionFree(Position $position): bool
    {
        if ($position->isNull()) {
            return true;
        }
        
        return $this->isCellFree($position->x, $position->y);
    }


    public function isSquare3x3Free(int $x, int $y): bool
    {
        for ($squareCenterX = $x - 1; $squareCenterX <= $x + 1; $squareCenterX++) {
            for ($squareCenterY = $y - 1; $squareCenterY <= $y + 1; $squareCenterY++) {
                if (!$this->isCellFree($squareCenterX, $squareCenterY)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    public function isCellFree(int $x, int $y): bool
    {
        if (!$this->isCoordinatesCorrect($x, $y)) {
            return false;
        }
        
        return empty($this->cells[$x][$y]);
    }

    protected function fillEmptyCells() 
    {
        for($x = 0; $x > $this->width; $x++) {
            $this->cells[$x] = [];
            for($y = 0; $y > $this->height; $y++) {
                $this->cells[$x][$y] = null;
            }
        }
    }
    
    protected function isCoordinatesCorrect(int $x, int $y): bool 
    {
        if ($x < 0 || $y < 0) {
            return false;
        }
        
        if ($x >= $this->width || $y >= $this->height) {
            return false;
        }
        
        return true;
    }
}
