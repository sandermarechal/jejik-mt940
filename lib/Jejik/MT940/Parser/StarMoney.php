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
 * StarMoney provides a parser for StarMoney Bank
 *
 * @package Jejik\MT940\Parser
 */
class StarMoney extends GermanBank {
    /**
     * Check whether provided MT940 statement string can be parsed by this parser
     */
    public function accept(string $text): bool
    {
        // unique identifier check
        $identifierCheck = substr($text, 0, 16) === ':20:REFSTARMONEY';
        if ($identifierCheck) {
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
        return ['10070024'];
    }
}
