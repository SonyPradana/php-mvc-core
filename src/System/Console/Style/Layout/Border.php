<?php

declare(strict_types=1);

namespace System\Console\Style\Layout;

use System\Console\Interfaces\Border as InterfacesBorder;
use System\Console\Style\Color\ForegroundColor;
use System\Console\Style\Colors;

class Border implements InterfacesBorder
{
    private ForegroundColor $color;
    private bool $top;
    private bool $right;
    private bool $bottom;
    private bool $left;

    /** @var string[] */
    private array $corner = ['╮', '╯', '╰', '╭'];

    public function __construct(
        ForegroundColor $color,
        bool $top = true,
        bool $right = true,
        bool $bottom = true,
        bool $left = true
    ) {
        $this->color  = $color ?? Colors::hexText('#ffffff');
        $this->top    = $top;
        $this->right  = $right;
        $this->bottom = $bottom;
        $this->left   = $left;
    }

    public function color(): ForegroundColor
    {
        return $this->color;
    }

    public function top(): bool
    {
        return $this->top;
    }

    public function right(): bool
    {
        return $this->right;
    }

    public function bottom(): bool
    {
        return $this->bottom;
    }

    public function left(): bool
    {
        return $this->left;
    }

    public function topLeft(): bool
    {
        return $this->top() && $this->left();
    }

    public function topRight(): bool
    {
        return $this->top() && $this->right();
    }

    public function bottomRight(): bool
    {
        return $this->bottom() && $this->right();
    }

    public function bottomLeft(): bool
    {
        return $this->bottom() && $this->left();
    }

    /**
     * order:
     * 0: top-right
     * 1: bottom-right
     * 2: bottom-left
     * 3: top-left.
     *
     * @param string[] $corner
     */
    public function cornerStyle(array $corner): self
    {
        $this->corner = $corner;

        return $this;
    }

    public function corner(): array
    {
        return $this->corner;
    }
}
