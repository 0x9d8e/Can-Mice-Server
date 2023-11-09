<?php

namespace App\PersonalEvents;

use App\Definitions\PersonalEventTypeDefinition;

class YouWillBeRessurected implements PersonalEventInterface
{
    protected int $ressurect_at;
    
    public function __construct(int $ressurectAt)
    {
        $this->ressurect_at = $ressurectAt;
    }

    public function getId(): string
    {
        return PersonalEventTypeDefinition::YOU_WILL_BE_RESSURECTED;
    }
    
    public function getData(): array
    {
        return [
            'ressurect_at' => $this->ressurect_at,
        ];
    }
}
