<?php


namespace App;

class Map 
{
    public int $width;
    public int $height;
    
    public array $cells = [];
   
    
    public function __construct(int $width, int $height) 
    {
        $this->width = $width;
        $this->height = $height;
        
        $this->fillEmptyCells();
    }
    
    public function add(MapObject $object): void
    {
        $position = $object->getPosition();
        if (! $position->isNull()) {
            if (!empty($this->cells[$position->x][$position->y])) {
                throw new \Exception("Can not add object to position {$position}. Position is not empty!");
            }
            
            $this->cells[$position->x][$position->y] = $object;
        }
    }
    
    public function get(Position $position): MapObject
    {
        return $this->cells[$position->x][$position->y] ?? throw new \Exception("Has not object at {$position}");
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
        
        throw new \Exception('Can not find free position on map!');
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
