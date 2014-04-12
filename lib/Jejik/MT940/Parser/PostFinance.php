<?php

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\MT940\Parser;

/**
 * Parser for PostFinance documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class PostFinance extends AbstractParser
{
    /**
     * Test if the document is an ABN-AMRO document
     *
     * @param string $text
     * @return bool
     */
    public function accept($text)
    {
        return strpos(strtok($text, "\n"), 'POFICH') !== false;
    }

    /**
     * Get the closing balance
     *
     * @param mixed $text
     * @return void
     */
    protected function closingBalance($text)
    {
        if ($line = $this->getLine('62M', $text)) {
            return $this->balance($this->reader->createClosingBalance(), $line);
        }
    }

    /**
     * Get the contra account number from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     * @return string|null
     */
    protected function contraAccountNumber(array $lines)
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
}
