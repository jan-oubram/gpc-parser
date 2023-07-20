<?php

use Stancl\GPC\Record;

test('record strings can be parsed to Record instances', function () {
    $string = '0750000000123456789000000098765432100000123456780000000123001000000123400000000000000000000200723Objednávka 1234     00203200723';

    $record = Record::fromString($string);

    expect($record->localAccount)->toBe('123456789');
    expect($record->foreignAccount)->toBe('987654321');
    expect($record->transactionId)->toBe('12345678');
    expect($record->amount)->toBe('12300');
    expect($record->type)->toBe('1');
    expect($record->variableSymbol)->toBe('1234');
    expect($record->constantSymbol)->toBe('');
    expect($record->specificSymbol)->toBe('');
    expect($record->valuta)->toBe('200723');
    expect($record->message)->toBe('Objednávka 1234');
    expect($record->currencyCode)->toBe('0203');
    expect($record->date)->toBe('200723');
});

test('Record instances can be converted to record strings', function () {
    $record = new Record(
        localAccount: '123456789',
        foreignAccount: '987654321',
        transactionId: '12345678',
        amount: '12300',
        type: '1',
        variableSymbol: '1234',
        foreignBankCode: '0123',
        constantSymbol: '',
        specificSymbol: '',
        valuta: '200723',
        message: 'Objednávka 1234',
        currencyCode: '0203',
        date: '200723',
    );

    expect((string) $record)
        ->toBe('0750000000123456789000000098765432100000123456780000000123001000000123400012300000000000000200723Objednávka 1234     00203200723');
});
