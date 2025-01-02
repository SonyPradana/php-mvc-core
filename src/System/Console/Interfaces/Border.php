<?php

namespace System\Console\Interfaces;

use System\Console\Style\Color\ForegroundColor;

interface Border
{
    public function color(): ForegroundColor;

    public function top(): bool;

    public function right(): bool;

    public function bottom(): bool;

    public function left(): bool;

    // extend

    public function topLeft(): bool;

    public function topRight(): bool;

    public function bottomRight(): bool;

    public function bottomLeft(): bool;

    public function corner(): array;
}
