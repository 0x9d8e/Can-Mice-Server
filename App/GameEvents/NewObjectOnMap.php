<?php

namespace App\GameEvents;

use App\Definitions\GameEventTypeDefinition;
use App\MapObjectInterface;

/**
 * When a new object is added to the map
 */
class NewObjectOnMap implements GameEventInterface
{
    protected MapObjectInterface $object;

    public function __construct(MapObjectInterface $object)
    {
        $this->object = $object;
    }

    public function getId(): string
    {
        return GameEventTypeDefinition::NEW_OBJECT_ON_MAP;
    }

    public function getData(): array
    {
        $object = $this->object;
        
        return [
            'id' => $object->getMapObjectId(),
            'type' => $object->getTypeId(),
            'position' => $object
                ->getPosition()
                ->toArray(),
            'type_specific_data' => $object->getTypeSpecificData(),
        ];
    }
}
