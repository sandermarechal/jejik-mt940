<?php

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
 * Parser for ING documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Ing extends AbstractParser
{
    /**
     * Test if the document is an ING document
     *
     * @param string $text
     * @return bool
     */
    public function accept($text)
    {
        return substr($text, 6, 6) === 'INGBNL';
    }

    /**
     * Parse a statement number
     *
     * @param string $text Statement body text
     * @return string|null
     */
    protected function statementNumber($text)
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
     * @return \Jejik\MT940\Transaction
     */
    protected function transaction(array $lines)
    {
        $transaction = parent::transaction($lines);
        $transaction->setBookDate($transaction->getValueDate())
                    ->setValueDate(null);

        if (preg_match('/transactiedatum: (\d{2}-\d{2}-\d{4})/', $lines[1], $match)) {
            $valueDate = \DateTime::createFromFormat('d-m-Y', $match[1]);
            $valueDate->setTime(0, 0, 0);

            $transaction->setValueDate($valueDate);
        }

        return $transaction;
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     * @return string|null
     */
    protected function contraAccountNumber(array $lines)
    {
        if (preg_match('/^([0-9]{9,10}) /', $lines[1], $match)) {
            return $match[1];
        }

        return null;
    }
}
