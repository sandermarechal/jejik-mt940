<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2020 Powercloud GmbH <d.richter@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\MT940\Parser;

/**
 * Commerzbank provides a parser for Commerz Bank
 * @package Jejik\MT940\Parser
 */
class Commerzbank extends GermanBank
{
    /**
     * Check whether provided MT940 statement string can be parsed by this parser
     */
    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:012CIXCIA7V1OGWA',
            ':20:0157VSNLKBG9WGWA',
            ':20:01LGX08DLMWH5GWA',
        ];

        // unique identifier check
        $mt940Identifier = substr($text, 0, 20);

        if (in_array($mt940Identifier, $allowedUniqueIdentifiers, true)) {
            return true;
        }

        // if not check it's BLZ
        return $this->isBLZAllowed($text);
    }

    /**
     * Get an array of allowed BLZ for this bank
     * @return array
     */
    public function getAllowedBLZ(): array
    {
        return [
            '70040041',
            '66280053',
            '28540034',
            '25040066',
        ];
    }
}
