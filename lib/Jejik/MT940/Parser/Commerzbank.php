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
            ':20:B2NG0OPCF3PTM87C',
            ':20:B2NG0MGUR8GUXUW8',
            ':20:01LGX08DLMWH5GWA',
            ':20:01WFLM6I6YHV0GWA',
            ':20:241104EF55E6F763',
            ':20:02XJKL3H7ZRM1CDE',
            ':20:230401A1B2C3D4E5',
            ':20:05ABC12345DEF678',
            ':20:230708F4C1A0D9G7',
        ];

        // unique identifier check
        $mt940Identifier = substr($text, 0, 20);
        if (in_array(strtoupper($mt940Identifier), $allowedUniqueIdentifiers, true)) {
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
            '50040000',
            '16040000',
            '25040066',
            '36040039'
        ];
    }
}
