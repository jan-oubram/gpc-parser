<?php

use Stancl\GPC\Lines\HeaderLine;
use Stancl\GPC\Parser;

test('a single file can be parsed', function () {
    $parser = new Parser;

    $parser->parseFile(__DIR__ . '/../etc/sample.gpc');

    expect($parser->lines)->toHaveCount(6);
    expect($parser->records())->toHaveCount(5);

    // Header line
    expect($parser->lines[0])->toBeInstanceOf(HeaderLine::class);
    expect((string) $parser->lines[0])->toBe('0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111              ');

    // Records
    expect($parser->records()[0]->localAccount)->toBe("123456789");
    expect($parser->records()[0]->foreignAccount)->toBe("987654321");
    expect($parser->records()[0]->transactionId)->toBe("12345678");
    expect($parser->records()[0]->amount)->toBe("12300");
    expect($parser->records()[0]->type)->toBe("1");
    expect($parser->records()[0]->variableSymbol)->toBe("1234");
    expect($parser->records()[0]->constantSymbol)->toBe("");
    expect($parser->records()[0]->specificSymbol)->toBe("");
    expect($parser->records()[0]->valuta)->toBe("200723");
    expect($parser->records()[0]->message)->toBe("Objednávka č. 1234");
    expect($parser->records()[0]->currencyCode)->toBe("0203");
    expect($parser->records()[0]->date)->toBe("200723");

    expect((string) $parser->records()[0])->toBe('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723');
    expect((string) $parser->records()[1])->toBe('0750000000123456789000000098765432100000183456780000000123001000000123500000000000000000000200723Objednávka 1235     00203200723');
});

test('a string can be parsed', function () {
    $parser = new Parser;

    $parser->parse('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723');

    expect($parser->lines)->toHaveCount(1);
    expect($parser->records())->toHaveCount(1);

    // Records
    expect($parser->records()[0]->localAccount)->toBe("123456789");
    expect($parser->records()[0]->foreignAccount)->toBe("987654321");
    expect($parser->records()[0]->transactionId)->toBe("12345678");
    expect($parser->records()[0]->amount)->toBe("12300");
    expect($parser->records()[0]->type)->toBe("1");
    expect($parser->records()[0]->variableSymbol)->toBe("1234");
    expect($parser->records()[0]->constantSymbol)->toBe("");
    expect($parser->records()[0]->specificSymbol)->toBe("");
    expect($parser->records()[0]->valuta)->toBe("200723");
    expect($parser->records()[0]->message)->toBe("Objednávka č. 1234");
    expect($parser->records()[0]->currencyCode)->toBe("0203");
    expect($parser->records()[0]->date)->toBe("200723");
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

    expect($parser->records()[0]->variableSymbol)->toBe('1234');

    $parser->records()[0]->variableSymbol = 'ABC01234';

    expect($parser->records()[0]->variableSymbol)->toBe('ABC01234');
});

test('parsed content can be converted back into a gpc string', function () {
    $parser = new Parser;

    $parser->parse('0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111              ');
    $parser->parse('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723');
    $parser->parse('0750000000123456789000000098765432100000183456780000000123001000000123500000000000000000000200723Objednávka 1235     00203200723');

    expect($parser->lines)->toHaveCount(3);
    expect($parser->records())->toHaveCount(2);

    expect($parser->content())->toBe("0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111              \r\n0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723\r\n0750000000123456789000000098765432100000183456780000000123001000000123500000000000000000000200723Objednávka 1235     00203200723\r\n");
});

test('constant symbol is parsed and generated correctly', function () {
    /**
     * We get the bank code from the constant symbol *segment* which includes padding, bank code, and constant symbol.
     *
     * This test verifies that the symbol is parsed and generated correctly
     *
     * @see \Stancl\GPC\Record
     */

    $parser = new Parser;

    // Difference between these two is that one has a bank number of 1200, and the other 1234
    // This is mostly a regression test for when trailing zeros in the bank code caused issues with parsing the constant symbol and lead to it being e.g. 0056 instead of 0567
    $record1 = '0750000001234567899000000987654321100112233445560000000500002012345678900120005670000000000200723Example payment     00203200723';
    $record2 = '0750000001234567899000000987654321100112233445560000000500002012345678900123405670000000000200723Example payment     00203200723';

    $parser->parse($record1);
    $parser->parse($record2);

    expect($parser->records()[0]->foreignBankCode)->toBe('1200');
    expect($parser->records()[1]->foreignBankCode)->toBe('1234');

    expect($parser->records()[0]->constantSymbol)->toBe('0567');
    expect($parser->records()[1]->constantSymbol)->toBe('0567');

    expect($parser->content())->toBe("0750000001234567899000000987654321100112233445560000000500002012345678900120005670000000000200723Example payment     00203200723\r\n0750000001234567899000000987654321100112233445560000000500002012345678900123405670000000000200723Example payment     00203200723\r\n");
});
