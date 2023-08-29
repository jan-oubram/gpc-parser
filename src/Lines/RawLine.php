<?php

declare(strict_types=1);

namespace Stancl\GPC\Lines;

use Stancl\GPC\Contracts\Line;

/**
 * (Previously) used for the record header (074 / 'Data - výpis v Kč') that we didn't need to parse.
 *
 * Now the header is parsed by HeaderLine
 *
 * @see HeaderLine
 */
class RawLine implements Line
{
    public string $content;

    public function __construct(string $line)
    {
        $this->content = $line;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
