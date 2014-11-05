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
 * Parser for Triodos documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Triodos extends AbstractParser
{
    /**
     * Test if the document is an ABN-AMRO document
     *
     * @param string $text
     * @return bool
     */
    public function accept($text)
    {
        return strpos($text, ':25:TRIODOSBANK') !== false;
    }

    /**
     * Parse a account number
     *
     * Remove the TRIODOSBANK/ prefix
     *
     * @param string $text Statement body text
     * @return string|null
     */
    protected function accountNumber($text)
    {
        if ($account = $this->getLine('25', $text)) {
            return ltrim(substr($account, 12), '0');
        }

        return null;
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     * @return string|null
     */
    protected function contraAccountNumber(array $lines)
    {
        if (preg_match('/^000>1([0-9]{11})/', $lines[1], $match)) {
            return ltrim($match[1], '0');
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    protected function description($description)
    {
        return preg_replace('/>2[0-7]{1}/', '', $description);
    }
}
