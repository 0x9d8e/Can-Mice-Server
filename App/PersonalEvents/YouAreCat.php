<?php

namespace App\PersonalEvents;

use App\Definitions\PersonalEventTypeDefinition;

class YouAreCat implements PersonalEventInterface
{
    public function getId(): string
    {
        return PersonalEventTypeDefinition::YOU_ARE_CAT;
    }
    
    public function getData(): array
    {
        return [];
    }
}
