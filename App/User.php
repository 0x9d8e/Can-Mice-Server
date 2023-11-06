<?php

namespace App;

use App\Commands\AbstractCommand;
use App\Commands\CommandFabric;
use App\Commands\ExitCommand;
use Exception;
use React\Socket\ConnectionInterface;
use SplQueue;

class User implements MapObjectInterface
{
    const STATUS_OFFLINE = 0;
    const STATUS_WAITING = 1;
    const STATUS_MICE = 2;
    const STATUS_CAT = 3;
    const STATUS_DEAD = 4;
    const STATUS_FINISHED = 5;

    public string $login;
    
    public int $goals = 0;
    public int $record = 0;
    
    public Position $position;
    
    public int $status = self::STATUS_OFFLINE;
    
    public Game $game;
    
    public SplQueue $command_queue;
    
    public ConnectionInterface $connection;
    
    protected int $map_object_id;
    
    public static function makeByLogin(string $login, Game $game, Position $position): Self
    {
        $user = new User($game);
        $user->login = $login;
        $user->position = $position;
        
        return $user;
    }
    
    public static function makeByConnection(ConnectionInterface $connection, Game $game): Self
    {
        $user = new User($game);
        $user->position = Position::makeNull();
        $user->setConnection($connection);
        
        return $user;
    }

    protected function __construct(Game $game) 
    {
        $this->game = $game;
        $this->command_queue = new SplQueue();
    }
    
    public function setConnection(ConnectionInterface $connection): void
    {
        $this->connection = $connection;
        
        $connection->on(
            'data', 
            function ($data) use ($connection) {
                $command = (new CommandFabric($this->game, $this))->make($data);

                $this->addCommand($command);
            }
        );
                
        $this->connection->on(
            'close', 
            function () {
                echo ' Connection closed! ';

                $this->addCommand(
                    new ExitCommand($this->game, $this->connection, [])
                );
            }
        );
    }
    
    public function auth(string $login): void
    {
        if ($this->isAuthorized()) {
            throw new Exception('User is already authorized!');
        }
        $this->login = $login;
        
        switch ($this->game->status) {
            case Game::STATUS_WAITING:
                $this->wait();
                break;
            case Game::STATUS_GAME:
                $this->play();
                break;
            case Game::STATUS_RECORD_TABLE:
                $this->finish();
                break;
        }
    }
    
    public function isAuthorized(): bool
    {
        return isset($this->login) && $this->status !== self::STATUS_OFFLINE;
    }

    public function wait(): void
    {
        $this->status = self::STATUS_WAITING;
        $this->connection->write(" status wait! ");
    }
    
    public function play(): void
    {
        $this->position = $this->game->map->getFreePosition();
        $this->game->map->add($this);
        $this->status = self::STATUS_MICE;
        $this->connection->write(" PLAY! status MICE! ");
    }
    
    public function die(): void
    {
        if ($this->goals) {
            $this->goals = round($this->goals / 2);
        }
        
        $this->game->map->freePosition($this->position);
        $this->position = Position::makeNull();
        $this->status = self::STATUS_DEAD;
        
        $this->connection->write(" YOU ARE DEAD! ");
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
        $this->connection->write(" status CAT! ");
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
        $this->connection->write(" FINISH! ");
    }

    public function getPosition(): Position 
    {
        return $this->position;
    }

    public function setPosition(Position $position): void 
    {
        $this->position = $position;
    }

    public function touch(MapObjectInterface $object, Position $newPosition): void 
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
        if ($this->command_queue->isEmpty()) {
            return;
        }
        
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

    public function getTypeId(): string
    {
        return 'user';
    }
    
    public function getTypeSpecificData(): array
    {
        return [
            'login' => $this->login,
            'status' => $this->status,
            'goals' => $this->goals,
            'record' => $this->record,
        ];
    }
    
    public function getMapObjectId(): int
    {
        return $this->map_object_id;
    }
    
    public function setMapObjectId(int $id): void
    {
        $this->map_object_id = $id;
    }
}
