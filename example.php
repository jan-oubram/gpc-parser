<?php

use Stancl\GPC\Lines\RecordLine;
use Stancl\GPC\Parser;

require './vendor/autoload.php';

// This example transforms a GPC file by prefixing the VS of all transactions coming from a certain bank account
// Usage: php example.php your-gpc-file.gpc <bank account number> <VS prefix>

$file = $argv[1];

$parser = new Parser;

$parser->parseFile($file);

collect($parser->records())
    ->filter(fn (RecordLine $line) => $line->record->foreignAccount === $argv[2] && $line->record->variableSymbol)
    ->each(fn (RecordLine $line) => $line->record->variableSymbol = $argv[3] . $line->record->variableSymbol);

$parser->writeContent($argv[1] . '-modified-test.gpc');
