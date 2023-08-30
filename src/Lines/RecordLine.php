<?php

declare(strict_types=1);

namespace Stancl\GPC\Lines;

use Stancl\GPC\Contracts\Line;
use Stancl\GPC\Record;

class RecordLine implements Line
{
    public Record $record;

    public function __construct(string|Record $record)
    {
        $this->record = $record instanceof Record
            ? $record
            : Record::fromString($record);
    }

    public function __toString(): string
    {
        return (string) $this->record;
    }
}
