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
 * DeutscheBank provides a parser for Deutsche Bank
 * @package Jejik\MT940\Parser
 */
class DeutscheBank extends GermanBank {
    /**
     * Check whether provided MT940 statement string can be parsed by this parser
     */
    public function accept(string $text): bool
    {
        // unique identifier check
        $allowedUniqueIdentifiers = [
            ':20:DEUTDEFF',
            ':20:DEUTDEMM',
            ':20:DEUTDEBB'
        ];

        // unique identifier check
        $mt940Identifier = substr($text, 0, 12);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        // if not check it's BLZ
        return $this->isBLZAllowed($text);
    }

    /**
     * Get an array of allowed BLZ for this bank
     */
    public function getAllowedBLZ(): array {
        return [
            '66470035'
        ];
    }
}
