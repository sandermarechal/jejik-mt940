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
 * Sparkasse provides a parser for Sparkasse Bank
 * @package Jejik\MT940\Parser
 */
class Sparkasse extends GermanBank {
    /**
     * Get an array of allowed BLZ for this bank
     */
    public function getAllowedBLZ(): array
    {
        return [
            '50550020'
        ];
    }

    /**
     * Test if the document can be read by the parser
     */
    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:STARTUMS'
        ];

        // unique identifier check
        $mt940Identifier = substr($text, 0, 12);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        // if not check it's BLZ
        return $this->isBLZAllowed($text);
    }
}
