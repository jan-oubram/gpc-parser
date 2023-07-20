<?php

declare(strict_types=1);

namespace Stancl\GPC;

use Stancl\GPC\Contracts\Line;

class Generator
{
    public array $lines = [];

    public function __construct(
        public string $encoding = 'windows-1250',
        public string $lineSeparator = "\r\n"
    ) {}

    /**
     * Add lines that will be used to generate the string GPC output.
     *
     * @param Line[] $lines
     * @return static
     */
    public function loadLines(array $lines): static
    {
        foreach ($lines as $line) {
            if ($line instanceof Line) {
                $this->lines[] = $line;
            }
        }

        return $this;
    }

    /**
     * NOTE: This method returns the string in UTF-8, not the specified encoding.
     *
     * The encoding is only applied when writing a file using writeContent().
     *
     * The point of this is to remove encoding as a variable when working with the data in PHP, and only handle it when reading or writing files.
     *
     * @return string
     */
    public function content(): string
    {
        return collect($this->lines)->reduce(function (string $content, Line $line) {
            return $content . (string) $line . $this->lineSeparator;
        }, '');
    }

    /**
     * Write the data to a file using the specified encoding.
     *
     * @param string $path
     * @return bool
     */
    public function writeContent(string $path): bool
    {
        return file_put_contents($path, iconv('utf-8', $this->encoding, $this->content())) !== false;
    }
}
