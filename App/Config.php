<?php

namespace App;

class Config 
{
    public string $host;
    public int $port;
    
    public float $main_loop_period = 1.0;
    
    public int $game_width = 40;
    public int $game_height = 40;
    
    public string $wait_before_start = '10 seconds';
    public string $game_duration = '1 minutes';
    public string $ressurection_cooldown = '5 seconds';
    public string $score_table_duration = '1 minutes';
    
    public function __construct()
    {
        $this->host = $_ENV['HOST'];
        $this->port = $_ENV['PORT'];
    }
}
