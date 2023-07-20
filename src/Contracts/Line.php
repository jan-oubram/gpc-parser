<?php

declare(strict_types=1);

namespace Stancl\GPC\Contracts;

use Stringable;

/**
 * Line within a .gpc file
 */
interface Line extends Stringable
{
    public function __construct(string $line);
}
