<?php

namespace App;

abstract class AbstractCommand 
{
    public Game $game;

    abstract function call();
}
