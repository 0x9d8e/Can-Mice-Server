<?php

namespace App;

class Game 
{
    const STATUS_WAITING = 0;
    const STATUS_GAME = 1;
    const STATUS_RECORD_TABLE = 2;
    
    public int $status;

    public Map $map;
    
    /**
     * 
     * @var User[]
     */
    public array $users = [];
    
    public \DateTimeImmutable $created_at;
    public \DateTimeImmutable $start_at;
    public \DateTimeImmutable $finish_at;
    public \DateTimeImmutable $remove_at;
    
    /**
     * Messages for players
     * 
     * @var \Ds\Queue
     */
    public \Ds\Queue $messages_queue;
    
    /**
     * Dead players waiting here
     * 
     * @var \Ds\Queue
     */
    public \Ds\Queue $ressurection_queue;
    public \DateTimeImmutable $next_ressurection_at;

    public function __construct() 
    {
        $this->status = self::STATUS_WAITING;
        $this->created_at = new \DateTimeImmutable();
        $this->start_at = $this->created_at->add(\DateInterval::createFromDateString('2 minutes'));
        $this->next_ressurection_at = $this->start_at->add(\DateInterval::createFromDateString('5 seconds'));
        $this->finish_at = $this->start_at->add(\DateInterval::createFromDateString('5 minutes'));
        $this->remove_at = $this->finish_at->add(\DateInterval::createFromDateString('30 minutes'));
    }
    
    public function loop(): bool
    {
        switch ($this->status) {
            case self::STATUS_WAITING:
                if ($this->start_at->getTimestamp() <= (new \DateTimeImmutable())->getTimestamp()) {
                    $this->start();
                }
                
                break;
            case self::STATUS_GAME:
                if ($this->finish_at->getTimestamp() <= (new \DateTimeImmutable())->getTimestamp()) {
                    $this->finish();
                } else {
                    $this->play();
                }

                break;
            case self::STATUS_RECORD_TABLE:
                if ($this->remove_at->getTimestamp() <= (new \DateTimeImmutable())->getTimestamp()) {
                    $this->remove();
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    public function makeMap(): void
    {
        $this->map = new Map(40, 40);
    }
    
    public function addUser(string $login): void
    {
        $user = new User($login, $this, $this->map->getFreePosition());
        $this->users[] = $user;
      
        switch ($this->status) {
            case self::STATUS_WAITING:
                $user->wait();
                break;
            case self::STATUS_GAME:
                $user->play();
                break;
            case self::STATUS_RECORD_TABLE:
                $user->finish();
                break;
        }
    }
    
    public function start(): void 
    {
        $this->status = self::STATUS_GAME;
        foreach ($this->users as $user) {
            $user->play();
        }
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
    }
    
    public function remove(): void 
    {
        // do nothing
    }
    
    protected function executeCommands()
    {
        foreach ($this->users as $user) {
            $user->callCommand();
        }
    }

    protected function ressurection() 
    {
        if ($this->next_ressurection_at->getTimestamp() <= (new \DateTimeImmutable())->getTimestamp()) {
            $user = $this->ressurection_queue->pop();
            if ($user) {
                $user->play();
            }

            $this->next_ressurection_at = $this->next_ressurection_at->add(\DateInterval::createFromDateString('5 seconds'));
        }
    }
    
    protected function sendMessages() 
    {
        $message = $this->messages_queue->pop();
        if ($message) {
            // todo
        }
    }
}
