<?php

namespace App;

use App\GameEvents\GameEventHandler;
use App\GameEvents\GameFinish;
use App\GameEvents\GameStarts;
use App\GameEvents\NewObjectPositionsOnMap;
use DateInterval;
use DateTimeImmutable;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\ServerInterface;
use SplQueue;

class Game 
{
    const STATUS_WAITING = 0;
    const STATUS_GAME = 1;
    const STATUS_RECORD_TABLE = 2;
    
    public int $status;

    public Config $config;
    public Map $map;
    public ServerInterface $socket;

    /**
     * 
     * @var User[]
     */
    public array $users = [];
    
    public DateTimeImmutable $created_at;
    public DateTimeImmutable $start_at;
    public DateTimeImmutable $finish_at;
    public DateTimeImmutable $remove_at;
    
    /**
     * Messages for players
     */
    public SplQueue $messages_queue;
    
    /**
     * Dead players waiting here
     */
    public SplQueue $ressurection_queue;
    public DateTimeImmutable $next_ressurection_at;
    
    public LoopInterface $loop;
    public $timer;

    public function __construct(LoopInterface $loop, Config $config, ServerInterface $socket) 
    {
        $this->status = self::STATUS_WAITING;
        
        $this->loop = $loop;
        $this->config = $config;
        $this->socket = $socket;
        
        $this->messages_queue = new SplQueue();
        $this->ressurection_queue = new SplQueue();
        
        $this->created_at = new DateTimeImmutable();
        $this->start_at = $this->created_at->add(
            DateInterval::createFromDateString($this->config->wait_before_start)
        );
        $this->next_ressurection_at = $this->start_at->add(
            DateInterval::createFromDateString($this->config->ressurection_cooldown)
        );
        $this->finish_at = $this->start_at->add(
            DateInterval::createFromDateString($this->config->game_duration)
        );
        $this->remove_at = $this->finish_at->add(
            DateInterval::createFromDateString($this->config->score_table_duration)
        );

        $this->makeMap();
    }
    
    public function run(): void
    {
        $this->loop->addPeriodicTimer(
            $this->config->main_loop_period, 
            function ($timer) {
                $this->timer = $timer;
                $this->loop();
                echo '.';
            }
        );

        $this->socket->on(
            'connection', 
            function (ConnectionInterface $connection) {
                echo " User connected! ";
                $this->addUser(User::makeByConnection($connection, $this));
            }
        );
    }

    public function loop(): bool
    {
        switch ($this->status) {
            case self::STATUS_WAITING:
                if ($this->start_at->getTimestamp() <= (new DateTimeImmutable())->getTimestamp()) {
                    echo " Game starts! ";
                    $this->start();
                }
                
                break;
            case self::STATUS_GAME:
                if ($this->finish_at->getTimestamp() <= (new DateTimeImmutable())->getTimestamp()) {
                    $this->finish();
                    echo " Game finish! ";
                } else {
                    $this->play();
                    echo " Play! ";
                }

                break;
            case self::STATUS_RECORD_TABLE:
                if ($this->remove_at->getTimestamp() <= (new DateTimeImmutable())->getTimestamp()) {
                    $this->remove();
                    echo " Game remowed! ";
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    public function makeMap(): void
    {
        $this->map = new Map($this->config->game_width, $this->config->game_height);
    }
    
    public function addUser(User $user): User
    {     
        $this->users[] = $user;
        
        return $user;
    }
    
    public function start(): void 
    {
        $this->status = self::STATUS_GAME;
        foreach ($this->users as $user) {
            if ($user->isAuthorized()) {
                $user->play();
            }
        }
        
        (new GameEventHandler($this))->handle(
            new GameStarts($this->finish_at->getTimestamp())
        );
    }
    
    public function play(): void
    {
        $this->executeCommands();
        
        $this->ressurection();
        
        $this->sendMessages();
    }

    public function finish(): void
    {
        $this->status = self::STATUS_RECORD_TABLE;
        foreach ($this->users as $user) {
            $user->finish();
        }
        
        (new GameEventHandler($this))->handle(
            new GameFinish([]) // todo: make score table
        );
    }
    
    public function remove(): void 
    {
        $this->loop->cancelTimer($this->timer);
    }
    
    protected function executeCommands()
    {
        foreach ($this->users as $user) {
            $user->callCommand();
        }
    }

    protected function ressurection() 
    {
        if ($this->next_ressurection_at->getTimestamp() <= (new DateTimeImmutable())->getTimestamp()) {
            if ($this->ressurection_queue->isEmpty()) {
                return;
            }
            $user = $this->ressurection_queue->pop();
            if ($user) {
                $user->play();
            }

            $this->next_ressurection_at = $this->next_ressurection_at->add(
                DateInterval::createFromDateString($this->config->ressurection_cooldown)
            );
        }
    }
    
    protected function sendMessages() 
    {
        $handler = new GameEventHandler($this);
        
        $objects = $this->map->getObjects();
        if (!empty($objects)) {
            $handler->handle(new NewObjectPositionsOnMap($objects));
        }
        
        if ($this->messages_queue->isEmpty()) {
            return;
        }
        $message = $this->messages_queue->pop();
        if ($message) {
            // todo
        }
    }
}
