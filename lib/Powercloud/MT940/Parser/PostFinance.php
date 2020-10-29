<?php

declare(strict_types=1);

/*
 * This file is part of the Powercloud\MT940 (a Fork of: Jejik\MT940) library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Powercloud\MT940\Parser;

use Powercloud\MT940\Balance;

/**
 * Parser for PostFinance documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class PostFinance extends AbstractParser
{
    /**
     * Test if the document is a PostFinance document
     */
    public function accept(string $text): bool
    {
        if (empty($text)) {
            return false;
        }
        return strpos(strtok($text, "\n"), 'POFICH') !== false;
    }

    /**
     * Get the closing balance
     */
    protected function closingBalance(string $text): ?Balance
    {
        if ($line = $this->getLine('62M', $text)) {
            return $this->balance($this->reader->createClosingBalance(), $line);
        }

        return null;
    }

    /**
     * Get the contra account number from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     */
    protected function contraAccountNumber(array $lines): ?string
    {
        if (!preg_match('/\n(\d{8})\d{7}(\d{8})/', $lines[0], $match)) {
            return null;
        }

        $date = substr($match[1], 2);
        $number = $match[2];

        if (preg_match(sprintf('/%sCH%s/', $date, $number), $lines[1])) {
            return $number;
        }

        return null;
    }

    /**
     * Get an array of allowed BLZ for this bank
     */
    public function getAllowedBLZ(): array
    {
        return [];
    }
}
