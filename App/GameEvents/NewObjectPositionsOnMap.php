<?php

namespace App\GameEvents;

use App\MapObjectInterface;

/**
 * New positions of objects on the map
 */
class NewObjectPositionsOnMap implements GameEventInterface
{
    /**
     * 
     * @var MapObjectInterface[]
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
        return 'new_object_positions_on_map';
    }

    public function getData(): array
    {
        return array_map(
            function(MapObjectInterface $object) {
                $position = $object->getPosition();

                return [
                    'id' => $object->getMapObjectId(),
                    'position' => [
                        'x' => $position->x,
                        'y' => $position->y,
                    ],
                ];
            }, 
            $this->objects,
        );
    }
}
