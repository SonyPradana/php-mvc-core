<?php

declare(strict_types=1);

namespace System\Console\ValueObjects;

/**
 * @internal
 */
class Direction
{
    private int $top    = 0;
    private int $right  = 0;
    private int $bottom = 0;
    private int $left   = 0;

    public function __construct(array $direction)
    {
        $this->top    = $direction[0];
        $this->right  = $direction[1];
        $this->bottom = $direction[2];
        $this->left   = $direction[3];
    }

    public function top(): int
    {
        return $this->top;
    }

    public function right(): int
    {
        return $this->right;
    }

    public function bottom(): int
    {
        return $this->bottom;
    }

    public function left(): int
    {
        return $this->left;
    }
}
