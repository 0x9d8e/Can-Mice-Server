<?php

namespace App\Definitions;

class PersonalEventTypeDefinition implements DefinitionInterface
{
    const YOU_ARE_ADDED_ON_MAP = 'you_are_added_on_map';
    const YOU_ARE_MOUSE = 'you_are_mouse';
    const YOU_ARE_CAT = 'you_are_cat';
    const YOU_ARE_DEAD = 'you_are_dead';
    const GOAL = 'goal';
    const YOU_WILL_BE_RESSURECTED = 'you_will_be_ressurected';
    
    public static function all(): array
    {
        return [
            self::YOU_ARE_ADDED_ON_MAP,
            self::YOU_ARE_MOUSE,
            self::YOU_ARE_CAT,
            self::YOU_ARE_DEAD,
            self::GOAL,
            self::YOU_WILL_BE_RESSURECTED,
        ];
    }
}
