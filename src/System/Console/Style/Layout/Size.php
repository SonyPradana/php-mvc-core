<?php

declare(strict_types=1);

namespace System\Console\Style\Layout;

use System\Console\Interfaces\BoxSize;
use System\Console\ValueObjects\Direction;

class Size implements BoxSize
{
    private int $width  = 0;
    private int $height = 0;
    private Direction $margin;
    private Direction $padding;

    public function __construct(
        int $width,
        int $height = null,
        Direction $margin = null,
        Direction $padding = null,
    ) {
        $this->width   = $width;
        $this->height  = $height ?? $width;
        $this->margin  = $margin ?? new Direction([0, 0, 0, 0]);
        $this->padding = $padding ?? new Direction([0, 0, 0, 0]);
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function margin(): Direction
    {
        return $this->margin;
    }

    public function padding(): Direction
    {
        return $this->padding;
    }
}
