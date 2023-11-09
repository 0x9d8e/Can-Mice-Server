<?php

namespace App;

class Position 
{
    public null|int $x;
    public null|int $y;
    
    public function __construct(null|int $x, null|int $y) {
        $this->x = $x;
        $this->y = $y;
    }
    
    public function __toString() 
    {
        return "{{$this->x}, {$this->y}}";
    }
    
    public function toArray(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }

    public static function makeNull(): Position
    {
        return new Position(null, null);
    }

    public function isNull(): bool
    {
        return is_null($this->x) || is_null($this->y);
    }

    public function up(): self
    {
        $this->y--;
        
        return $this;
    }
    
    public function down(): self
    {
        $this->y++;
        
        return $this;
    }
    
    public function left(): self
    {
        $this->x--;
        
        return $this;
    }
    
    public function right(): self
    {
        $this->x++;
        
        return $this;
    }
}
