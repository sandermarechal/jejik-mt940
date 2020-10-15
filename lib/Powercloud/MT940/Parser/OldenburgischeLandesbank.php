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
 * OldenburgischeLandesbank provides a parser for Oldenburgische Landesbank
 * @package Powercloud\MT940\Parser
 */
class OldenburgischeLandesbank extends GermanBank
{
    /**
     * Test if the document can be read by the parser
     */
    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:STARTUMS TA',
            ':20:STARTUMS MC',
        ];

        // unique identifier check
        $mt940Identifier = substr($text, 0, 15);
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
            '28020050',
            '25621327',
            '26520017',
            '26521703',
            '26522319',
            '26620010',
            '26621413',
            '26720028',
            '28021002',
            '28021301',
            '28021504',
            '28021623',
            '28021705',
            '28021906',
            '28022015',
            '28022412',
            '28022511',
            '28022620',
            '28022822',
            '28023224',
            '28023325',
            '28030300',
            '28220026',
            '28222208',
            '28222621',
            '28320014',
        ];
    }
}
