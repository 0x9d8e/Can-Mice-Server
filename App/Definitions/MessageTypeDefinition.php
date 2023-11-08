<?php

namespace App\Definitions;

class MessageTypeDefinition implements DefinitionInterface
{
    const GAME_EVENT = 'game_event';
    
    public static function all(): array
    {
        return [
            self::GAME_EVENT,
        ];
    }
}
