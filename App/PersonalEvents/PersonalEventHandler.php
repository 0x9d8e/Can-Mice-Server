<?php

namespace App\PersonalEvents;

use App\Definitions\MessageTypeDefinition;
use App\User;
use App\Message;

class PersonalEventHandler
{
    protected User $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function handle(PersonalEventInterface $event): void
    {
        if (!$this->user->isOnline()) {
            return;
        }

        $this->user->write(
            new Message(
                MessageTypeDefinition::PERSONAL_EVENT, 
                [
                    'event_type_id' => $event->getId(),
                    'event_data' => $event->getData(),
                ]
            )
        );
    }
}
