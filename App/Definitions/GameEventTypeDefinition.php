<?php

namespace App\Definitions;

class GameEventTypeDefinition implements DefinitionInterface
{
    // When a new object is added to the map
    const NEW_OBJECT_ON_MAP = 'new_object_on_map';
    
    // Send to game clients current positions of all objects on the map
    const NEW_OBJECT_POSITION_ON_MAP = 'new_object_positions_on_map';
    
    // Notify all game clients of the full status of objects on the map
    const FULL_MAP_STATE = 'full_map_state';

    public static function all(): array
    {
        return [
            self::NEW_OBJECT_ON_MAP,
            self::FULL_MAP_STATE,
        ];
    }
}
