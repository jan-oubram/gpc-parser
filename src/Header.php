<?php

declare(strict_types=1);

namespace Stancl\GPC;

use Stringable;

class Header implements Stringable
{
    public function __construct(
        // "074"
        public string $accountNumber, // 16 bytes; left padded with 0s. Potentially dashes turned into 000, but that doesn't apply to us, so we don't handle this case at the moment.
        public string $accountName, // 20 bytes; right padded with spaces
        public string $oldBalanceDate, // 6 bytes; DDMMRR
        public string $oldBalance, // 14 bytes; left padded with 0s, in cents
        public string $oldBalanceSign, // 1 byte; either "+" or "-"
        public string $newBalance, // 14 bytes; left padded with 0s, in cents
        public string $newBalanceSign, // 1 byte; either "+" or "-"
        public string $debetAmount, // 14 bytes; left padded with 0s, in cents
        public string $debetSign, // 1 byte; either "0" or "-"
        public string $creditAmount, // 14 bytes; left padded with 0s, in cents
        public string $creditSign, // 1 byte; either "0" or "-"
        public string $statementNumber, // 3 bytes; "pořadové číslo výpisu"
        public string $statementDate, // 6 bytes; DDMRR
        // 14 spaces
        // CRLF is trimmed/added in Parser/Generator so we don't have to deal with it here
    ) {
    }

    public static function fromString(string $record): static
    {
        $str = str($record);

        // Substr numbers here follow the bytes described here https://www.fio.cz/docs/cz/struktura-gpc.pdf, with the starting point subtracting one since we count from 0, not from 1
        // (e.g. "074" is bytes 1-3, but it's substr(0, 3) — starting from the first (zeroth) character with a length of 3)

        // $str->substr(starting byte - 1, segment length)

        return new static(
            // "074"
            accountNumber: $str->substr(3, 16)->ltrim('0')->toString(),
            accountName: $str->substr(19, 20)->rtrim(' ')->toString(),
            oldBalanceDate: $str->substr(39, 6)->toString(),
            oldBalance: $str->substr(45, 14)->ltrim('0')->toString(),
            oldBalanceSign: $str->substr(59, 1)->toString(),
            newBalance: $str->substr(60, 14)->ltrim('0')->toString(),
            newBalanceSign: $str->substr(74, 1)->toString(),
            debetAmount: $str->substr(75, 14)->ltrim('0')->toString(),
            debetSign: $str->substr(89, 1)->toString(),
            creditAmount: $str->substr(90, 14)->ltrim('0')->toString(),
            creditSign: $str->substr(104, 1)->toString(),
            statementNumber: $str->substr(105, 3)->ltrim('0')->toString() ?: '0', // the left padding is not in spec but handle it in case a single digit is used
            statementDate: $str->substr(108, 6)->toString(),
            // 14 spaces
        );
    }

    public function __toString(): string
    {
        return str('074')
            ->append(str($this->accountNumber)->padLeft(16, '0'))
            ->append(str($this->accountName)->padRight(20, ' '))
            ->append($this->oldBalanceDate)
            ->append(str($this->oldBalance)->padLeft(14, '0'))
            ->append($this->oldBalanceSign)
            ->append(str($this->newBalance)->padLeft(14, '0'))
            ->append($this->newBalanceSign)
            ->append(str($this->debetAmount)->padLeft(14, '0'))
            ->append($this->debetSign)
            ->append(str($this->creditAmount)->padLeft(14, '0'))
            ->append($this->creditSign)
            ->append(str($this->statementNumber)->padLeft(3, '0'))
            ->append($this->statementDate)
            ->append(str(' ')->repeat(14))
            ->toString();
    }
}
