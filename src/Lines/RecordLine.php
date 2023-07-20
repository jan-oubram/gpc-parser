<?php

declare(strict_types=1);

namespace Stancl\GPC\Lines;

use Stancl\GPC\Contracts\Line;
use Stancl\GPC\Record;

class RecordLine implements Line
{
    public Record $record;

    public function __construct(string $line)
    {
        $this->record = Record::fromString($line);
    }

    public function __toString(): string
    {
        return (string) $this->record;
    }
}
