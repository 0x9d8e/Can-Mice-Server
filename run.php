<?php

use App\Config;
use App\Game;
use React\EventLoop\Loop;
use React\Socket\SocketServer;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$config = new Config();
$socket = new SocketServer("{$config->host}:{$config->port}");

echo "Server running at {$config->host}:{$config->port}", PHP_EOL;

$game = new Game(Loop::get(), new Config(), $socket);
$game->run();

echo PHP_EOL;
