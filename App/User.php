<?php

namespace App;

class User implements MapObject
{
    const STATUS_OFFLINE = 0;
    const STATUS_WAITING = 1;
    const STATUS_MICE = 2;
    const STATUS_CAT = 3;
    const STATUS_DEAD = 4;
    const STATUS_FINISHED = 5;

    public string $login;
    
    public integer $goals = 0;
    public integer $record = 0;
    
    public Position $position;
    
    public integer $status = self::STATUS_OFFLINE;
    
    public Game $game;
    
    public \Ds\Queue $command_queue;

    public function __construct(string $login, Game $game, Position $position) 
    {
        $this->login = $login;
        $this->game = $game;
        $this->position = $position;
        
        $this->command_queue = new \Ds\Queue();
    }
    
    public function wait(): void
    {
        $this->status = self::STATUS_WAITING;
    }
    
    public function play(): void
    {
        $this->position = $this->game->map->getFreePosition();
        $this->game->map->add($this);
        $this->status = self::STATUS_MICE;
    }
    
    public function die(): void
    {
        if ($this->goals) {
            $this->goals = round($this->goals / 2);
        }
        
        $this->game->map->freePosition($this->position);
        $this->position = Position::makeNull();
        $this->status = self::STATUS_DEAD;
        
        $this->game->ressurection_queue->push($this);
    }
    
    public function exit(): void
    {
        $this->game->map->freePosition($this->position);
        $this->status = self::STATUS_OFFLINE;
    }


    public function becomeCat() 
    {
        $this->status = self::STATUS_CAT;
    }
    
    public function goal() 
    {
        $this->goals++;
    }

    public function finish(): void
    {
        $this->record = max($this->record, $this->goals);
        $this->position = Position::makeNull();
        $this->status = self::STATUS_FINISHED;
    }

    public function getPosition(): Position 
    {
        return $this->position;
    }

    public function setPosition(Position $position): void 
    {
        $this->position = $position;
    }

    public function touch(MapObject $object, Position $newPosition): void 
    {
        if ($object->isWall()) {
            return;
        }
        
        if ($object->canFight()) {
            $this->die();
            return;
        }
        
        switch ($this->status) {
            case self::STATUS_MICE:
                if ($object->canBeMouseFood()) {
                    $this->moveTo($newPosition);
                    $this->becomeCat();                  
                    return;
                }
                break;
            case self::STATUS_CAT:
                if ($object->canBeCatFood()) {
                    $this->moveTo($newPosition);
                    $this->goal();
                    return;
                }
                break;
            default:
                return;
        }
    }

    public function canBeCatFood(): bool 
    {
        return $this->status == self::STATUS_MICE;
    }

    public function canBeMouseFood(): bool 
    {
        return false;
    }

    public function canFight(): bool 
    {
        return $this->status == self::STATUS_CAT;
    }

    public function isWall(): bool 
    {
        return false;
    }
    
    public function addCommand(AbstractCommand $command): void
    {
        $this->command_queue->push($command);
    }

    public function callCommand(): void
    {
        $command = $this->command_queue->pop();
        if ($command) {
            $command->call();
        }
    }


    protected function moveTo(Position $newPosition): void
    {
        $this->game->map->freePosition($newPosition);
        $this->setPosition($newPosition);
    }
}
