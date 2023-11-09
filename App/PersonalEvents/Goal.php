<?php

namespace App\PersonalEvents;

use App\Definitions\PersonalEventTypeDefinition;

class Goal implements PersonalEventInterface
{
    public function getId(): string
    {
        return PersonalEventTypeDefinition::GOAL;
    }
    
    public function getData(): array
    {
        return [];
    }
}
