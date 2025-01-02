<?php

namespace System\Console\Interfaces;

use System\Console\ValueObjects\Direction;

interface BoxSize
{
    public function width(): int;

    public function height(): int;

    public function margin(): Direction;

    public function padding(): Direction;
}
