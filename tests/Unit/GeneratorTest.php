<?php

use Stancl\GPC\Generator;
use Stancl\GPC\Lines\RawLine;
use Stancl\GPC\Lines\RecordLine;

test('the content method returns a utf-8 string with the lines converted to a string', function () {
    $generator = new Generator;

    $generator->loadLines([
        new RawLine('0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111ABC'),
        new RecordLine('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723'),
    ]);

    expect($generator->content())->toBe("0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111ABC\r\n0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723\r\n");
    expect(mb_detect_encoding($generator->content()))->toBe('UTF-8');

    // Trying to convert from Windows-1250 to UTF-8 results in a broken string that doesn't match $generator->content(), confirming it's NOT in Windows-1250
    expect(iconv($generator->encoding, 'UTF-8', $generator->content()))->not()->toBe($generator->content());
});

test('the writeContent method writes a string with the specified encoding', function () {
    $generator = new Generator;

    $generator->loadLines([
        new RawLine('0740001111111111111Full name           01234000111111111111+00011111111111+000111111111111111111111111111111111111ABC'),
        new RecordLine('0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka č. 1234  00203200723'),
    ]);

    expect($generator->writeContent(__DIR__ . '/../etc/write_test.gpc'))->toBeTrue();

    // Opposite of this check in the previous test.
    // We check that converting the written string from Windows-1250 to UTF-8 *does* make the string equal to the one returned from $generator->content()
    expect(iconv($generator->encoding, 'UTF-8', file_get_contents(__DIR__ . '/../etc/write_test.gpc')))->toBe($generator->content());
});
