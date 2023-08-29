<?php

declare(strict_types=1);

namespace Stancl\GPC\Lines;

use Stancl\GPC\Contracts\Line;
use Stancl\GPC\Header;

class HeaderLine implements Line
{
    public Header $header;

    public function __construct(string $line)
    {
        $this->header = Header::fromString($line);
    }

    public function __toString(): string
    {
        return (string) $this->header;
    }
}
