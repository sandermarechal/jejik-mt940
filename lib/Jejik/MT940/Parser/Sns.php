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
     * @var string PCRE sub expression for the delimiter
     */
    protected $statementDelimiter = '-\}.*';

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
}
