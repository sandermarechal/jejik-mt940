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

use Jejik\MT940\TransactionInterface;

/**
 * Parser for ING documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Ing extends AbstractParser
{
    /**
     * Codewords from ING Format Description
     */
    public const CODEWORD_PREF = '/PREF/';
    public const CODEWORD_RTRN = '/RTRN/';
    public const CODEWORD_CREF = '/CREF/';
    public const CODEWORD_EREF = '/EREF/';
    public const CODEWORD_IREF = '/IREF/';
    public const CODEWORD_MARF = '/MARF/';
    public const CODEWORD_CSID = '/CSID/';
    public const CODEWORD_CNTP = '/CNTP/';
    public const CODEWORD_PURP = '/PURP/';
    public const CODEWORD_ULTC = '/ULTC/';
    public const CODEWORD_ULTD = '/ULTD/';
    public const CODEWORD_EXCH = '/EXCH/';
    public const CODEWORD_CHGS = '/CHGS/';
    public const CODEWORD_REMI_USTD = '/REMI/USTD//';

    /**
     * Test if the document is an ING document
     */
    public function accept(string $text): bool
    {
        if (empty($text)) {
            return false;
        }
        return substr(preg_replace('/\s/','',$text), 6, 6) === 'INGBNL';
    }

    /**
     * Parse a statement number
     *
     * @param string $text Statement body text
     */
    protected function statementNumber(string $text): ?string
    {
        if ($number = $this->getLine('28C', $text)) {
            return $number;
        }

        return null;
    }

    /**
     * Create a Transaction from MT940 transaction text lines
     *
     * ING only provides a book date, not a valuation date. This
     * is opposite from standard MT940 so the AbstractReader will read it
     * as a valueDate. This must be corrected.
     *
     * ING does sometimes supplies a book date inside the description.
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     *
     * @throws \Exception
     */
    protected function transaction(array $lines): TransactionInterface
    {
        $transaction = parent::transaction($lines);
        $transaction
            ->setBookDate($transaction->getValueDate())
            ->setValueDate(null);

        if (preg_match('/transactiedatum: (\d{2}-\d{2}-\d{4})/', $lines[1], $match)) {
            $valueDate = \DateTime::createFromFormat('d-m-Y', $match[1]);
            $valueDate->setTime(0, 0, 0);

            $transaction->setValueDate($valueDate);
        }

        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $lines[1], $match)) {
            $valueDate = \DateTime::createFromFormat('d/m/Y', $match[1]);
            $valueDate->setTime(0, 0, 0);

            $transaction->setValueDate($valueDate);
        }

        return $transaction;
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     */
    protected function contraAccountNumber(array $lines): ?string
    {
        if (preg_match('/^([0-9]{9,10}) /', $lines[1], $match)) {
            return $match[1];
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
     * @param array $lines
     * @return array|null
     */
    protected function codeWords(array $lines): ?array
    {
        $descriptionLine = $lines[1] ?? null;
        $multiUseLine = $this->removeNewLinesFromLine($descriptionLine);

        $identifiers = [
            static::CODEWORD_PREF,
            static::CODEWORD_RTRN,
            static::CODEWORD_CREF,
            static::CODEWORD_EREF,
            static::CODEWORD_IREF,
            static::CODEWORD_MARF,
            static::CODEWORD_CSID,
            static::CODEWORD_CNTP,
            static::CODEWORD_REMI_USTD,
            static::CODEWORD_PURP,
            static::CODEWORD_ULTC,
            static::CODEWORD_ULTD,
            static::CODEWORD_EXCH,
            static::CODEWORD_CHGS
        ];

        $regex = sprintf(
            '#(%s)#',
            implode('|', $identifiers)
        );

        $splitReferenceLine = preg_split(
            $regex,
            $multiUseLine,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );
        $codeWords = [];

        if (current($splitReferenceLine) === ':86:') {
            next($splitReferenceLine);
        }

        do {
            $fieldIdentifier = current($splitReferenceLine) ?: 'unknown';
            $fieldContent = next($splitReferenceLine) ?: null;
            $codeWords[$fieldIdentifier] = $fieldContent;
        } while (next($splitReferenceLine) !== false);

        return $codeWords;
    }

    /**
     * @param string $stringLine
     * @return string
     */
    private function removeNewLinesFromLine(string $stringLine): string
    {
        return str_replace(["\n", "\r", "\r\n"], '', $stringLine);
    }

    /**
     * @param array $lines
     * @return void|null
     */
    protected function transactionCode(array $lines)
    {
        $statementLine = $lines[0] ?? null;
        $multiUseLine = $this->removeNewLinesFromLine($statementLine);

        preg_match('#(\/TRCD\/)(\d{5})#', $multiUseLine, $match);

        return $match[2] ?? null;
    }

    protected function code(array $lines): ?string
    {
        // get :61: line -- it is first in provided array [:61:,:86:,....]
        $codeLine = $lines[0] ?? null;

        if ($codeLine == null) {
            return null;
        }

        preg_match('#(\d{6})(\d{4})?(R?(?:C|D))([0-9,]{1,15})N([a-zA-Z0-9]+)#', $codeLine, $match);

        if (!isset($match[5])) {
            return null;
        }

        return substr($match[5], 0, 3);
    }

    /**
     * @param array $lines
     * @return string|null
     */
    protected function eref(array $lines): ?string
    {
        if (isset($this->codeWords($lines)[self::CODEWORD_EREF]) || isset($this->codeWords($lines)[self::CODEWORD_REMI_USTD])) {
            return sprintf('%s %s', $this->codeWords($lines)[self::CODEWORD_EREF], $this->codeWords($lines)[self::CODEWORD_REMI_USTD]);
        }

        return null;
    }

    /**
     * @param array $lines
     * @return string|null
     */
    protected function rawSubfieldsData(array $lines): ?string
    {
        $descriptionLine = $lines[1] ?? null;
        return $this->removeNewLinesFromLine($descriptionLine);
    }

    /**
     * @param array $lines
     * @return string|null
     */
    protected function bic(array $lines): ?string
    {
        return $this->getCounterPartyId($lines)['bic'] ?? null;
    }

    /**
     * @param array $lines
     * @return string|null
     */
    protected function iban(array $lines): ?string
    {
        return isset($this->getCounterPartyId($lines)['accountNumber'])
            ? ($this->getCounterPartyId($lines)['accountNumber'])
            : null;
    }

    /**
     * @param array $lines
     * @return string|null
     */
    protected function accountHolder(array $lines): ?string
    {
        return $this->getCounterPartyId($lines)['name'] ?? null;
    }

    /**
     * @param array $lines
     * @return string|null
     */
    protected function txText(array $lines): ?string
    {
        return $this->codeWords($lines)[self::CODEWORD_REMI_USTD] ?? null;
    }

    /**
     * @param array $lines
     * @return array|null
     */
    protected function getCounterPartyId(array $lines): ?array
    {
        $cntp = $this->codeWords($lines)[self::CODEWORD_CNTP] ?? null;

        if (!$cntp) {
            return null;
        }

        $splitCodeWords = preg_split('#\/#', $cntp);

        return [
            'accountNumber' => $splitCodeWords[0],
            'bic' => $splitCodeWords[1],
            'name' => $splitCodeWords[2],
            'city' => $splitCodeWords[3]
        ];
    }

    /**
     * @param array $lines
     * @return false|string|null
     */
    protected function getCreditorId(array $lines)
    {
        return isset($this->codeWords($lines)[self::CODEWORD_CSID])
            ? substr($this->codeWords($lines)[self::CODEWORD_CSID], 0, strlen($this->codeWords($lines)[self::CODEWORD_CSID]) - 1)
            : null;
    }
}
