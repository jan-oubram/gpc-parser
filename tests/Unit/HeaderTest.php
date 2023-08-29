<?php

use Stancl\GPC\Header;

test('header strings can be converted to Header instances', function () {
    $string = '0740000000123456789John Doe            01082200000000012300+00000000014300+000000000020000000000000040000000310822';

    $header = Header::fromString($string);

    expect($header->accountNumber)->toBe('123456789');
    expect($header->accountName)->toBe('John Doe');
    expect($header->oldBalanceDate)->toBe('010822');
    expect($header->oldBalance)->toBe('12300');
    expect($header->oldBalanceSign)->toBe('+');
    expect($header->newBalance)->toBe('14300');
    expect($header->newBalanceSign)->toBe('+');
    expect($header->debetSign)->toBe('0');
    expect($header->debetAmount)->toBe('2000');
    expect($header->creditSign)->toBe('0');
    expect($header->creditAmount)->toBe('4000');
    expect($header->statementNumber)->toBe('0');
    expect($header->statementDate)->toBe('310822');
});

test('Header instances can be converted to header strings', function () {
    $header = new Header(
        accountNumber: '123456789',
        accountName: 'John Doe',
        oldBalanceDate: '010822',
        oldBalance: '12300',
        oldBalanceSign: '+',
        newBalance: '14300',
        newBalanceSign: '+',
        debetSign: '0',
        debetAmount: '2000',
        creditSign: '0',
        creditAmount: '4000',
        statementNumber: '0',
        statementDate: '310822',
    );

    expect((string) $header)
        ->toBe('0740000000123456789John Doe            01082200000000012300+00000000014300+000000000020000000000000040000000310822              ');
});
