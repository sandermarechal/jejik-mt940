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
 * Parser for SNS documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Sns extends AbstractParser
{
    /**
     * Test if the document is an SNS document
     *
     * @param string $text
     * @return bool
     */
    public function accept($text)
    {
        return substr($text, 6, 8) === 'SNSBNL2A';
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

        if (preg_match('/^([0-9]{10}) /', $lines[1], $match)) {
            return ltrim($match[1], '0');
        }

        return null;
    }
}
