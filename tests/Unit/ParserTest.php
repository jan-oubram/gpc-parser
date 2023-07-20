<?php

use Stancl\GPC\Lines\RawLine;
use Stancl\GPC\Parser;

test('a single file can be parsed', function () {
    $parser = new Parser;

    $parser->parseFile(__DIR__ . '/../etc/sample.gpc');

    expect($parser->lines)->toHaveCount(6);
    expect($parser->records())->toHaveCount(5);

    // Header line
    expect($parser->lines[0])->toBeInstanceOf(RawLine::class);
    expect($parser->lines[0]->content)->toBe('0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111ABC');
    expect((string) $parser->lines[0])->toBe($parser->lines[0]->content);

    // Records
    expect($parser->records()[0]->record->localAccount)->toBe("123456789");
    expect($parser->records()[0]->record->foreignAccount)->toBe("987654321");
    expect($parser->records()[0]->record->transactionId)->toBe("12345678");
    expect($parser->records()[0]->record->amount)->toBe("12300");
    expect($parser->records()[0]->record->type)->toBe("1");
    expect($parser->records()[0]->record->variableSymbol)->toBe("1234");
    expect($parser->records()[0]->record->constantSymbol)->toBe("");
    expect($parser->records()[0]->record->specificSymbol)->toBe("");
    expect($parser->records()[0]->record->valuta)->toBe("200723");
    expect($parser->records()[0]->record->message)->toBe("Objednávka č. 1234");
    expect($parser->records()[0]->record->currencyCode)->toBe("0203");
    expect($parser->records()[0]->record->date)->toBe("200723");

    expect((string) $parser->records()[0])->toBe('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723');
    expect((string) $parser->records()[1])->toBe('0750000000123456789000000098765432100000183456780000000123001000000123500000000000000000000200723Objednávka 1235     00203200723');
});

test('a string can be parsed', function () {
    $parser = new Parser;

    $parser->parse('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723');

    expect($parser->lines)->toHaveCount(1);
    expect($parser->records())->toHaveCount(1);

    // Records
    expect($parser->records()[0]->record->localAccount)->toBe("123456789");
    expect($parser->records()[0]->record->foreignAccount)->toBe("987654321");
    expect($parser->records()[0]->record->transactionId)->toBe("12345678");
    expect($parser->records()[0]->record->amount)->toBe("12300");
    expect($parser->records()[0]->record->type)->toBe("1");
    expect($parser->records()[0]->record->variableSymbol)->toBe("1234");
    expect($parser->records()[0]->record->constantSymbol)->toBe("");
    expect($parser->records()[0]->record->specificSymbol)->toBe("");
    expect($parser->records()[0]->record->valuta)->toBe("200723");
    expect($parser->records()[0]->record->message)->toBe("Objednávka č. 1234");
    expect($parser->records()[0]->record->currencyCode)->toBe("0203");
    expect($parser->records()[0]->record->date)->toBe("200723");
});

test('multiple sources can be parsed', function () {
    $parser = new Parser;

    $parser->parse('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723');
    $parser->parse('0750000000123456789000000098765432100000183456780000000123001000000123500000000000000000000200723Objednávka 1235     00203200723');

    expect($parser->lines)->toHaveCount(2);
    expect($parser->records())->toHaveCount(2);
});

test('parsed lines can be modified', function () {
    $parser = new Parser;

    $parser->parse('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723');

    expect($parser->records()[0]->record->variableSymbol)->toBe('1234');

    $parser->records()[0]->record->variableSymbol = 'ABC01234';

    expect($parser->records()[0]->record->variableSymbol)->toBe('ABC01234');
});

test('parsed content can be converted back to a gpc string', function () {
    $parser = new Parser;

    $parser->parse('0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111ABC');
    $parser->parse('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723');
    $parser->parse('0750000000123456789000000098765432100000183456780000000123001000000123500000000000000000000200723Objednávka 1235     00203200723');

    expect($parser->lines)->toHaveCount(3);
    expect($parser->records())->toHaveCount(2);

    expect($parser->content())->toBe("0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111ABC\r\n0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723\r\n0750000000123456789000000098765432100000183456780000000123001000000123500000000000000000000200723Objednávka 1235     00203200723\r\n");
});
