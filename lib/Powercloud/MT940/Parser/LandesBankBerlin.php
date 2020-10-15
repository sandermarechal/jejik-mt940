<?php

declare(strict_types=1);

/*
 * This file is part of the Powercloud\MT940 (a Fork of: Jejik\MT940) library
 *
 * Copyright (c) 2020 Powercloud GmbH <d.richter@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Powercloud\MT940\Parser;

/**
 * Class LandesBankBerlin provides a parser for Landes Bank Berlin
 * - LBB
 * @package Powercloud\MT940\Parser
 */
class LandesBankBerlin extends GermanBank
{
    /**
     * Check whether provided MT940 statement string can be parsed by this parser
     */
    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:FI-C53-ID',
        ];

        // unique identifier check
        $mt940Identifier = substr($text, 0, 13);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        // if not check it's BLZ
        return $this->isBLZAllowed($text);
    }

    /**
     * Get an array of allowed BLZ for this bank
     */
    public function getAllowedBLZ(): array
    {
        return [
            '10050000',
            '10050005',
            '10050006',
            '10050007',
            '10050008',
        ];
    }
}
