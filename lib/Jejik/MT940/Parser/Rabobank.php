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

use Jejik\MT940\Statement;

/**
 * Parser for Rabobank documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Rabobank extends AbstractParser
{
    private const FORMAT_CLASSIC = 1;
    private const FORMAT_STRUCTURED = 2;

    /**
     * @var int Document format
     */
    private $format = self::FORMAT_CLASSIC;

    /**
     * Test if the document is a RABO document
     */
    public function accept(string $text): bool
    {
        if (empty($text)) {
            return false;
        }
        return substr($text, 0, 5) === ':940:';
    }

    /**
     * Determine the format for this statement
     *
     * @param string $text Statement body text
     * @throws \Exception
     */
    protected function statementBody(string $text): Statement
    {
        switch (substr($this->getLine('20', $text), 0, 4)) {
            case '940A':
                $this->format = self::FORMAT_CLASSIC;
                break;
            case '940S':
                $this->format = self::FORMAT_STRUCTURED;
                break;
            default:
                throw new \RuntimeException('Unknown file format');
        }

        return parent::statementBody($text);
    }

    /**
     * Parse an account number
     *
     * @param string $text Statement body text
     */
    protected function accountNumber(string $text): ?string
    {
        $format = $this->format == self::FORMAT_CLASSIC ? '/^[0-9.]+/' : '/^[0-9A-Z]+/';
        if ($account = $this->getLine('25', $text)) {
            if (preg_match($format, $account, $match)) {
                return str_replace('.', '', $match[0]);
            }
        }

        return null;
    }

    /**
     * Rabobank does not use statement numbers. Use the opening balance
     * date as statement number instead.
     *
     * @param string $text Statement body text
     */
    protected function statementNumber(string $text): ?string
    {
        if ($line = $this->getLine('60F', $text)) {
            if (preg_match('/(C|D)(\d{6})([A-Z]{3})([0-9,]{1,15})/', $line, $match)) {
                return $match[2];
            }
        }

        return null;
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     */
    protected function contraAccountNumber(array $lines): ?string
    {
        switch ($this->format) {
            case self::FORMAT_CLASSIC:
                if (preg_match('/(\d{6})((?:C|D)R?)([0-9,]{15})(N\d{3}|NMSC)([0-9P ]{16})/', $lines[0], $match)) {
                    return rtrim(ltrim($match[5], '0P'));
                }
                break;

            case self::FORMAT_STRUCTURED:
                $parts = explode("\r\n", $lines[0]);

                if (2 === count($parts)) {
                    return $parts[1];
                }
                break;
        }

        return null;
    }

    /**
     * Get the contra account holder name from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     */
    protected function contraAccountName(array $lines): ?string
    {
        switch ($this->format) {
            case self::FORMAT_CLASSIC:
                if (preg_match('/(\d{6})((?:C|D)R?)([0-9,]{15})(N\d{3}|NMSC)([0-9P ]{16}|NONREF)(.*)/', $lines[0], $match)) {
                    return trim($match[6]) ?: null;
                }
                break;

            case self::FORMAT_STRUCTURED:
                if (preg_match('#/NAME/([^/]+)/#', $lines[1], $match)) {
                    return trim(str_replace("\r\n", '', $match[1])) ?: null;
                }
                break;
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
}
