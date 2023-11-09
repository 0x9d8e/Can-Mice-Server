<?php

namespace App\Definitions;

class MessageTypeDefinition implements DefinitionInterface
{
    const GLOBAL_GAME_EVENT = 'global_game_event';
    const PERSONAL_EVENT = 'personal_event';

    public static function all(): array
    {
        return [
            self::GLOBAL_GAME_EVENT,
            self::PERSONAL_EVENT,
        ];
    }
}
