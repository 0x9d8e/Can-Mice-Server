<?php

namespace App\GameEvents;

use App\Definitions\MessageTypeDefinition;
use App\Game;
use App\Message;

class GameEventHandler
{
    protected Game $game;
    
    public function __construct(Game $game)
    {
        $this->game = $game;
    }
    
    public function handle(GameEventInterface $event): void
    {
        foreach ($this->game->users as $user) {
            if (!$user->isOnline()) {
                return;
            }
            
            $user->write(
                new Message(
                    MessageTypeDefinition::GLOBAL_GAME_EVENT, 
                    [
                        'event_type_id' => $event->getId(),
                        'event_data' => $event->getData(),
                    ]
                )
            );
        }
    }
}
