<?php

namespace App\GameEvents;

/**
 * GameEventInterface is an interface for game event classes. 
 * When events occur in the game, the server needs to notify game clients 
 * about these events. Each event provides an event type identifier 
 * and serialised event data.
 */
interface GameEventInterface
{
    public function getId(): string;
    
    public function getData(): array;
}
