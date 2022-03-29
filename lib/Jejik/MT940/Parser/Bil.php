<?php

declare(strict_types=1);

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
 * Parser for Banque Internationale a Luxembourg
 *
 * Class Bil
 * @package Jejik\MT940\Parser
 *
 */
class Bil extends AbstractParser
{

    /**
     * @param string $text
     * @return bool
     */
    public function accept(string $text): bool
    {
        if (empty($text)) {
            return false;
        }
        {
            $allowedUniqueIdentifiers = [
                ':20:BILMT940',
            ];

            $mt940Identifier = substr($text, 0, 12);
            if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
                return true;
            }

            return $this->isBLZAllowed($text);
        }
    }

    /**
     * Get an array of allowed BLZ for this bank
     */
    public function getAllowedBLZ(): array
    {
        return [];
    }

    /**
     * @param array $lines
     * @return string|null
     */
    protected function gvc(array $lines): ?string
    {
        $gvcLine = $lines[1] ?? null;

        if ($gvcLine == null) {
            return null;
        }

        return substr($gvcLine, 0, 3);
    }

    /**
     * Parse txText for provided transaction lines
     */
    protected function txText(array $lines): ?string
    {
        $txTextLine = isset($lines[1]) ? $lines[1] : null;

        if ($txTextLine === null) {
            return null;
        }

        /** @var string $txTextLine */
        preg_match('#\?00([a-zA-Z0-9\-\s\.]+)#', $this->removeNewLinesFromLine($txTextLine), $match);

        if (!isset($match[1])) {
            return null;
        }

        return $match[1];
    }

    /**
     * Remove all new lines and carriage returns from provided input line
     */
    private function removeNewLinesFromLine(string $stringLine): string
    {
        return str_replace(["\n", "\r", "\r\n"], '', $stringLine);
    }

    /** Get raw data of subfields ?20 - ?29
     *
     * @param array $lines
     * @return string|string[]|null
     */
    protected function rawSubfieldsData(array $lines)
    {
        $subflieldline = isset($lines[1]) ? $lines[1] : null;

        $multiUseLine = $this->removeNewLinesFromLine($subflieldline);
        preg_match('#(\?2[0-9][^?]+)+#', $multiUseLine, $match);

        if (!isset($match[0])) {
            return null;
        }

        return preg_replace('#(\?2[0-9])#', '', $match[0]);
    }

    /**
     * Parse code for provided transaction lines
     */
    protected function code(array $lines): ?string
    {
        $codeLine = isset($lines[0]) ? $lines[0] : null;

        if ($codeLine == null) {
            return null;
        }
        preg_match('#(\d{6})(\d{4})?(R?(?:C|D)R?)([0-9,]{1,15})N([a-zA-Z0-9]+)#', $codeLine, $match);

        if (!isset($match[5])) {
            return null;
        }
        return substr($match[5], 0, 3);
    }

    /**
     * Parse ref for provided transaction lines
     */
    protected function ref(array $lines): ?string
    {

        $refLine = isset($lines[0]) ? $lines[0] : null;

        if ($refLine == null) {
            return null;
        }
        preg_match('#(?:\d{10})?(R?(?:C|D)R?)(?:[\d,]{1,15})N(.){3}([A-Za-z0-9\.]+)#', $refLine, $match);
        if (!isset($match[3])) {
            return null;
        }

        return $match[3];
    }
}

