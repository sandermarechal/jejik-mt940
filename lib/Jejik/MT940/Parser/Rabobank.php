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
 * Parser for Rabobank documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Rabobank extends AbstractParser
{
    /**
     * Test if the document is an ING document
     *
     * @param string $text
     * @return bool
     */
    public function accept($text)
    {
        return substr($text, 0, 5) === ':940:';
    }

    /**
     * Parse an account number
     *
     * @param string $text Statement body text
     * @return string|null
     */
    protected function accountNumber($text)
    {
        if ($account = $this->getLine('25', $text)) {
            if (preg_match('/^[0-9.]+/', $account, $match)) {
                return str_replace('.', '', $match[0]);
            }
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
        if (!preg_match('/(\d{6})((?:C|D)R?)([0-9,]{15})(N\d{3}|NMSC)([0-9P ]{16})/', $lines[0], $match)) {
            return null;
        }

        $contraAccount = rtrim(ltrim($match[5], '0P'));

        return $contraAccount;
    }
}
