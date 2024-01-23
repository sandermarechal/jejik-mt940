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

    protected function getTransactionLines(string $text): ?array
    {
        $amountLine = [];
        $pcre = '/(?:^|\r\n)\:(?:61)\:(.+)(?::?$|\r\n\:[[:alnum:]]{2,3}\:)/Us';

        if (preg_match_all($pcre, $text, $match)) {
            $amountLine = $match;
        }

        // here is a giro or sepa syntax possible
        // sepa begins with /TRTP/SEPA
        $multiPurposeField = [];
        $pcre = '/(?:^|\r\n)\:(?:86)\:(.+)(?:[\r\n])(?:\:(?:6[0-9]{1}[a-zA-Z]?)\:|(?:[\r\n]-$))/Us';

        if (preg_match_all($pcre, $text, $match)) {
            $multiPurposeField = $match;
        }

        $result = [];
        if (count($amountLine) === 0 && count($multiPurposeField) === 0) {
            return $result;
        }
        if ($amountLine[1] === null) {
            return $result;
        }

        $count = count($amountLine[1]);
        for ($i = 0; $i < $count; $i++) {
            $result[$i][] = trim($amountLine[1][$i]);
            $result[$i][] = trim(str_replace(':86:', '', $multiPurposeField[1][$i]));
        }

        return $result;
    }
}
