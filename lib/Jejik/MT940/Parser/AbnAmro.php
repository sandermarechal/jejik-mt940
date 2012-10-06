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
 * Parser for ABN-AMRO documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class AbnAmro extends AbstractParser
{
    /**
     * Test if the document is an ABN-AMRO document
     *
     * @param string $text
     * @return bool
     */
    public function accept($text)
    {
        return substr($text, 0, 6) === 'ABNANL';
    }

    /**
     * Get the opening balance
     *
     * @param mixed $text
     * @return void
     */
    protected function openingBalance($text)
    {
        if ($line = $this->getLine('60F|60M', $text)) {
            return $this->balance($this->reader->createOpeningBalance(), $line);
        }
    }

    /**
     * Get the closing balance
     *
     * @param mixed $text
     * @return void
     */
    protected function closingBalance($text)
    {
        if ($line = $this->getLine('62F|62M', $text)) {
            return $this->balance($this->reader->createClosingBalance(), $line);
        }
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     * @return string|null
     */
    protected function contraAccountNumber(array $lines)
    {
        if (!isset($lines[1])) {
            return null;
        }

        if (preg_match('/^([0-9.]{11,14}) /', $lines[1], $match)) {
            return str_replace('.', '', $match[1]);
        }

        if (preg_match('/^GIRO([0-9 ]{9}) /', $lines[1], $match)) {
            return trim($match[1]);
        }

        return null;
    }
}
