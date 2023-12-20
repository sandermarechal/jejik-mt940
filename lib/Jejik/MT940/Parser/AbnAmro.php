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
 * Parser for ABN-AMRO documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class AbnAmro extends AbstractParser
{
    /**
     * Test if the document is an ABN-AMRO document
     */
    public function accept(string $text): bool
    {
        if (empty($text)) {
            return false;
        }
        return substr($text, 0, 6) === 'ABNANL';
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     */
    protected function contraAccountNumber(array $lines): ?string
    {
        if (!isset($lines[1])) {
            return null;
        }

        if (preg_match('/^([0-9.]{11,14}) /', $lines[1], $match)) {
            return str_replace('.', '', $match[1]);
        }

        if (preg_match('/^GIRO([0-9 ]{9}) /', $lines[1], $match)) {
            return trim($match[1]);
        }

        return null;
    }

    /**
     * Get the contra account holder name from a transaction
     *
     * There is only a countra account name if there is a contra account number
     * The name immediately follows the number in the first 32 characters of the first line
     * If the charaters up to the 32nd after the number are blank, the name is found in
     * the rest of the line.
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     */
    protected function contraAccountName(array $lines): ?string
    {
        if (!isset($lines[1])) {
            return null;
        }

        $line = strstr($lines[1], "\r\n", true) ?: $lines[1];
        $offset = 0;

        if (preg_match('/^([0-9.]{11,14}) (.*)$/', $line, $match, PREG_OFFSET_CAPTURE)) {
            $offset = $match[2][1];
        }

        if (preg_match('/^GIRO([0-9 ]{9}) (.*)$/', $line, $match, PREG_OFFSET_CAPTURE)) {
            $offset = $match[2][1];
        }

        // No account number found, so no name either
        if (!$offset) {
            return null;
        }

        // Name in the first 32 characters
        if ($name = trim(substr($line, $offset, 32 - $offset))) {
            return $name;
        }

        // Name in the second 32 characters
        if ($name = trim(substr($line, 32, 32))) {
            return $name;
        }

        return null;
    }

    /**
     * Get an array of allowed BLZ for this bank
     */
    public function getAllowedBLZ(): array
    {
        return [];
    }

    /**
     * For this bank, the gvc is in line :61:
     *
     * @param array $lines
     * @return string|null
     */
    protected function gvc(array $lines): ?string
    {
        // get :61: line -- it is first in provided array [:61:,:86:,....]
        $codeLine = isset($lines[0]) ? $lines[0] : null;

        // assure code line
        if ($codeLine == null) {
            return null;
        }

        // match it
        preg_match('#(\d{6})(\d{4})?(R?(?:C|D))([0-9,]{1,15})N(\d{3})([a-zA-Z0-9]+)#', $codeLine, $match);

        // assure match
        if (!isset($match[5])) {
            return null;
        }

        // return
        return substr($match[5], 0, 3);
    }

    /** Get raw data of :86:
     *
     * @param array $lines
     * @return string|string[]|null
     */
    protected function rawSubfieldsData(array $lines)
    {
        $subflieldline = isset($lines[1]) ? $lines[1] : null;
        $multiUseLine = $this->removeNewLinesFromLine($subflieldline);

        return $multiUseLine;
    }

    /**
     * @param array $lines
     * @return string|null
     */
    protected function kref(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $krefLine = isset($lines[1]) ? $lines[1] : null;

        /** @var string $krefLine */
        preg_match("#(\/PREF\/)+([a-zA-ZöäüÖÄÜß0-9\-\+\.\_\s]+)?(\/NRTX\/)?(:62)?#", $this->removeNewLinesFromLine($krefLine), $match);

        if (!isset($match[2])) {
            return null;
        }

        return $match[2];
    }

    /**
     * @param string $stringLine
     * @return string
     */
    private function removeNewLinesFromLine(string $stringLine): string
    {
        return str_replace(["\n", "\r", "\r\n"], '', $stringLine);
    }
}
