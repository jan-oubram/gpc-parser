<?php

use Stancl\GPC\Parser;
use Stancl\GPC\Record;

require './vendor/autoload.php';

// This example transforms a GPC file by prefixing the VS of all transactions coming from a certain bank account
// Usage: php example.php your-gpc-file.gpc <bank account number> <VS prefix>

$file = $argv[1];

$parser = new Parser;

$parser->parseFile($file);

collect($parser->records())
    ->filter(fn (Record $record) => $record->foreignAccount === $argv[2] && $record->variableSymbol)
    ->each(fn (Record $record) => $record->variableSymbol = $argv[3] . $record->variableSymbol);

$parser->writeContent($argv[1] . '-modified-test.gpc');
