<?php

namespace App\GameEvents;

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
        return 'new_object_on_map';
    }

    public function getData(): array
    {
        $object = $this->object;
        
        return [
            'id' => $object->getMapObjectId(),
            'type' => $object->getTypeId(),
            'position' => [
                'x' => $object->x,
                'y' => $object->y,
            ],
            'type_specific_data' => $object->getTypeSpecificData(),
        ];
    }
}
