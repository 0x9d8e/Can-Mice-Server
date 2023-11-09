<?php

namespace App\GameEvents;

use App\Definitions\GameEventTypeDefinition;
use App\MapObjectInterface;

/**
 * Notify all game clients of the full status of objects on the map
 */
class FullMapState implements GameEventInterface
{
    /**
     * 
     * @var MapObjectInterface[] $objects
     */
    protected array $objects;

    /**
     * 
     * @param MapObjectInterface[] $objects
     */
    public function __construct(array $objects)
    {
        $this->objects = $objects;
    }

    public function getId(): string
    {
        return GameEventTypeDefinition::FULL_MAP_STATE;
    }

    public function getData(): array
    {
        return array_map(
            function (MapObjectInterface $object) {
                return [
                    'id' => $object->getMapObjectId(),
                    'type' => $object->getTypeId(),
                    'position' => $object
                        ->getPosition()
                        ->toArray(),
                    'type_specific_data' => $object->getTypeSpecificData(),
                ];
            },
            $this->objects
        );
        
    }
}
