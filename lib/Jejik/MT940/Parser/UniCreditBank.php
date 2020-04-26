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
 * UniCreditBank provides a parser for Unicredit Bank
 * - Hypovereinsbank
 * @package Jejik\MT940\Parser
 */
class UniCreditBank extends GermanBank {
    /**
     * Check whether provided MT940 statement string can be parsed by this parser
     */
    public function accept(string $text): bool
    {
        // unique identifier check
        $identifierCheck = false; // TODO implement after clearing field :20: with Bank
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
        return [
            '10020890',
            '16020086',
            '17020086',
            '18020086',
            '20030000',
            '20730001',
            '20730002',
            '20730003',
            '20730004',
            '20730005',
            '20730006',
            '20730007',
            '20730008',
            '20730009',
            '20730010',
            '20730011',
            '20730012',
            '20730013',
            '20730014',
            '20730015',
            '20730016',
            '20730017',
            '20730018',
            '20730019',
            '20730020',
            '20730021',
            '20730022',
            '20730023',
            '20730024',
            '20730025',
            '20730026',
            '20730027',
            '20730028',
            '20730029',
            '20730030',
            '20730031',
            '20730032',
            '20730033',
            '20730034',
            '20730035',
            '20730036',
            '20730037',
        ];
    }
}
