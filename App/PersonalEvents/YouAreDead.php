<?php

namespace App\PersonalEvents;

use App\Definitions\PersonalEventTypeDefinition;

class YouAreDead implements PersonalEventInterface
{
    public function getId(): string
    {
        return PersonalEventTypeDefinition::YOU_ARE_DEAD;
    }
    
    public function getData(): array
    {
        return [];
    }
}
