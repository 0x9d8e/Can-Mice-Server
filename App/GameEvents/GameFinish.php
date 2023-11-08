<?php

namespace App\GameEvents;

/**
 * When the game is over.
 */
class GameFinish implements GameEventInterface
{
    protected array $score_table;
    
    public function __construct(array $scoreTable)
    {
        $this->score_table = $scoreTable;
    }
    public function getId(): string
    {
        return 'game_finish';
    }

    public function getData(): array
    {
        return [
            'score_table' => $this->score_table,
        ];
    }
}
