<?php

declare(strict_types=1);

namespace System\Console\Style;

use System\Console\Interfaces\Border;
use System\Console\Interfaces\BoxSize;
use System\Text\Str;

class Box
{
    public const LEFT   = 0;
    public const RIGHT  = 1;
    public const CENTER = 2;

    private string $text;
    private BoxSize $boxSize;
    private Border $border;
    private int $left      = 0;
    private int $alignment = 0;

    public function __construct(
        string $text,
        BoxSize $boxSize,
        Border $border
    ) {
        $this->text    = $text;
        $this->boxSize = $boxSize;
        $this->border  = $border;
    }

    public function left(int $left): self
    {
        $this->left = $left;

        return $this;
    }

    public function textAlignment(int $alignment): self
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function render(): Style
    {
        $box_style = new Style();
        foreach ($this->renderLines() as $style) {
            // var_dump($style);
            $box_style->tap($style);
        }

        return $box_style;
    }

    /**
     * Order
     * - margin top
     * - border
     * - padding.
     *
     * @return Style[]
     */
    public function renderLines(): array
    {
        $margin_top    = new Style();
        $border_top    = new Style();
        $content       = new Style();
        $border_bottom = new Style();

        // render margin top
        $margin = $this->boxSize->margin()->top();
        foreach (range(1, $margin) as $m_top) {
            $margin_top
                ->repeat(' ', $this->left)
                ->repeat(' ', $this->boxSize->margin()->left())
                ->repeat(' ', $this->boxSize->width())
                ->new_lines()
            ;
        }

        // render border
        $border_top
            ->repeat(' ', $this->left)
            ->repeat(' ', $this->boxSize->margin()->left())
            ->push($this->border->corner()[3])
            ->textColor($this->border->color())
            ->repeat('─', $this->boxSize->width() + $this->boxSize->padding()->left() + $this->boxSize->padding()->right())
            ->textColor($this->border->color())

            ->push($this->border->corner()[0])
            ->textColor($this->border->color())
            ->repeat(' ', $this->boxSize->margin()->right())
            ->new_lines()
        ;

        // content
        foreach (range(0, $this->boxSize->padding()->top()) as $p) {
            $content
                ->repeat(' ', $this->left)
                ->repeat(' ', $this->boxSize->margin()->left())
                ->push('│')
                ->textColor($this->border->color())
                ->repeat(' ', $this->boxSize->width() + $this->boxSize->padding()->left() + $this->boxSize->padding()->right())
                ->push('│')
                ->textColor($this->border->color())
                ->new_lines()
            ;
        }
        foreach ($this->alignment($this->text, $this->alignment, $this->boxSize->width()) as $key => $value) {
            if ($key + 1 > $this->boxSize->height()) {
                $value = trim($value) . '...';
                $value = $value . str_repeat(' ', $this->boxSize->width() - strlen($value));
            }
            $content
                ->repeat(' ', $this->left)
                ->repeat(' ', $this->boxSize->margin()->left())
                ->push('│')
                ->textColor($this->border->color())
                ->repeat(' ', $this->boxSize->padding()->left())
                ->push($value)
                ->repeat(' ', $this->boxSize->padding()->right())
                ->push('│')
                ->textColor($this->border->color())
                ->repeat(' ', $this->boxSize->margin()->right())
                ->new_lines()
            ;
            if ($key + 1 > $this->boxSize->height()) {
                break;
            }
        }
        foreach (range(0, $this->boxSize->padding()->bottom()) as $p) {
            $content
                ->repeat(' ', $this->left)
                ->repeat(' ', $this->boxSize->margin()->left())
                ->push('│')
                ->textColor($this->border->color())
                ->repeat(' ', $this->boxSize->width() + $this->boxSize->padding()->left() + $this->boxSize->padding()->right())
                ->push('│')
                ->textColor($this->border->color())
                ->new_lines()
            ;
        }

        // bottom
        $border_bottom
            ->repeat(' ', $this->left)
            ->repeat(' ', $this->boxSize->margin()->left())
            ->push($this->border->corner()[2])
            ->textColor($this->border->color())
            ->repeat('─', $this->boxSize->width() + $this->boxSize->padding()->left() + $this->boxSize->padding()->right())
            ->textColor($this->border->color())
            ->push($this->border->corner()[1])
            ->textColor($this->border->color())
            ->repeat(' ', $this->boxSize->margin()->right())
            ->new_lines()
        ;

        return [
            'margin_top'    => $margin_top,
            'border_top'    => $border_top,
            'content'       => $content,
            'border_bottom' => $border_bottom,
            'margin_bottom' => $margin_top,
        ];
    }

    private function alignment(string $text, int $alignment, int $width)
    {
        $words = explode(' ', $text);
        $lines = [];
        $line  = 0;
        while (count($words) > 0) {
            if (!array_key_exists($line, $lines)) {
                $lines[$line] = array_shift($words);
                continue;
            }
            $word   = array_shift($words);
            $lenght = strlen($word) + strlen($lines[$line]) + 1;
            if ($lenght < $width) {
                $lines[$line] .= ' ' . $word;
                continue;
            }
            $line++;
            $lines[$line] = $word;
        }

        if ($alignment === static::RIGHT) {
            return array_map(
                fn ($value) => Str::fill($value, ' ', $width),
                $lines
            );
        }

        if ($alignment === static::CENTER) {
            return array_map(
                // fn($value) => Str::fill($value, ' ', $width),
                function ($value) use ($width) {
                    foreach (range(1, $width - strlen($value)) as $q) {
                        if ($q % 2 === 0) {
                            $value = ' ' . $value;
                        } else {
                            $value .= ' ';
                        }
                    }

                    return $value;
                },
                $lines
            );
        }

        return array_map(
            fn ($value) => Str::fillEnd($value, ' ', $width),
            $lines
        );
    }
}
