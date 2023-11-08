<?php

namespace App\GameEvents;

/**
 * When the game is starts
 */
class GameStarts implements GameEventInterface
{
    protected int $finish_at;
    
    public function __construct(int $finishAt)
    {
        $this->finish_at = $finishAt;
    }
    public function getId(): string
    {
        return 'game_starts';
    }

    public function getData(): array
    {
        return [
            'finish_at' => $this->finish_at,
        ];
    }
}
