<?php

namespace App\PersonalEvents;

use App\Definitions\PersonalEventTypeDefinition;

class YouAreMouse implements PersonalEventInterface
{
    public function getId(): string
    {
        return PersonalEventTypeDefinition::YOU_ARE_MOUSE;
    }
    
    public function getData(): array
    {
        return [];
    }
}
