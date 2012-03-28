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

use Jejik\MT940\Balance;
use Jejik\MT940\Statement;
use Jejik\MT940\Transaction;

/**
 * Base MT940 parser
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
abstract class AbstractParser
{
    /**
     * PCRE sub expression for the bank-specific statement footer
     *
     * / will be used as delimiter, so it must be escaped.
     *
     * @var string
     */
    protected $statementDelimiter = null;

    /**
     * Parse an MT940 document
     *
     * @param string $text Full document text
     * @return array An array of \Jejik\MT940\Statement
     */
    public function parse($text)
    {
        $statements = array();
        foreach ($this->splitStatements($text) as $chunk) {
            $statements[] = $this->statement($chunk);
        }

        return $statements;
    }

    /**
     * Get the contents of an MT940 line
     *
     * The contents may be several lines long (e.g. :86: descriptions)
     *
     * @param string $id The line ID (e.g. "20"). Can be a regular expression (e.g. "60F|60M")
     * @param string $text The text to search
     * @param int $offset The offset to start looking
     * @param int $position Starting position of the found line
     * @return string
     */
    protected function getLine($id, $text, $offset = 0, &$position = null)
    {
        $pcre = '/(?:^|\r\n)\:(' . $id . ')\:'   // ":<id>:" at the start of a line
              . '(.+)'                           // Contents of the line
              . '(:?$|\r\n\:[[:alnum:]]{2,3}\:)' // End of the text or next ":<id>:"
              . '/Us';                           // Ungreedy matching

        // Offset manually, so the start of the offset can match ^
        if (preg_match($pcre, substr($text, $offset), $match, PREG_OFFSET_CAPTURE)) {
            $position = $offset + $match[1][1] - 1;
            return rtrim($match[2][0]);
        }

        return '';
    }

    /**
     * Split the text into separate statement chunks
     *
     * @param string $text Full document text
     * @return array Array of statement texts
     * @throws \RuntimeException if the statementDelimiter is not set
     */
    protected function splitStatements($text)
    {
        if ($this->statementDelimiter !== null) {
            $chunks = preg_split('/^' . $this->statementDelimiter . '\r$/m', $text, -1);
            return array_filter(array_map('trim', $chunks));
        }

        throw new \RuntimeException('No statementDelimiter set');
    }

    /**
     * Split transactions and their descriptions from the statement text
     *
     * Returns a nexted array of transaction lines. The transaction line text
     * is at offset 0 and the description line text (if any) at offset 1.
     *
     * @param string $text Statement text
     * @return array Nested array of transaction and description lines
     */
    protected function splitTransactions($text)
    {
        $offset = 0;
        $position = 0;
        $transactions = array();

        while ($line = $this->getLine('61', $text, $offset, $offset)) {
            $offset += 4 + strlen($line) + 2;
            $transaction = array($line);

            // See if the next description line belongs to this transaction line.
            // The description line should immediately follow the transaction line.
            $description = array();
            while ($line = $this->getLine('86', $text, $offset, $position)) {
                if ($position == $offset) {
                    $offset += 4 + strlen($line) + 2;
                    $description[] = $line;
                } else {
                    break;
                }
            }

            if ($description) {
                $transaction[] = implode("\r\n", $description);
            }

            $transactions[] = $transaction;
        }

        return $transactions;
    }

    /**
     * Parse a statement chunk
     *
     * @param string $text Statement text
     * @return \Jejik\MT940\Statement
     * @throws \RuntimeException if the chunk cannot be parsed
     */
    protected function statement($text)
    {
        $text = trim($text);
        if (($pos = strpos($text, ':20:')) === false) {
            throw new \RuntimeException('Not an MT940 statement');
        }

        $this->statementHeader(substr($text, 0, $pos));
        return $this->statementBody(substr($text, $pos));
    }

    /**
     * Parse a statement header
     *
     * @param string $text Statement header text
     * @return void
     */
    protected function statementHeader($text)
    {
    }

    /**
     * Parse a statement body
     *
     * @param string $text Statement body text
     * @return \Jejik\MT940\Statement
     */
    protected function statementBody($text)
    {
        $statement = new Statement();
        $statement->setNumber($this->statementNumber($text))
                  ->setAccount($this->accountNumber($text))
                  ->setOpeningBalance($this->openingBalance($text))
                  ->setClosingBalance($this->closingBalance($text));

        foreach ($this->splitTransactions($text) as $chunk) {
            $statement->addTransaction($this->transaction($chunk));
        }

        return $statement;
    }

    /**
     * Parse a statement number
     *
     * @param string $text Statement body text
     * @return string|null
     */
    protected function statementNumber($text)
    {
        if ($number = $this->getLine('28|28C', $text)) {
            return $number;
        }

        return null;
    }

    /**
     * Parse an account number
     *
     * @param string $text Statement body text
     * @return string|null
     */
    protected function accountNumber($text)
    {
        if ($account = $this->getLine('25', $text)) {
            return ltrim($account, '0');
        }

        return null;
    }

    /**
     * Create a Balance object from an MT940  balance line
     *
     * @param string $text
     * @return \Jejik\MT940\Balance
     */
    protected function balance($text)
    {
        if (!preg_match('/(C|D)(\d{6})([A-Z]{3})([0-9,]{1,15})/', $text, $match)) {
            throw new \RuntimeException(sprintf('Cannot parse balance: "%s"', $text));
        }

        $amount = (float) str_replace(',', '.', $match[4]);
        if ($match[1] === 'D') {
            $amount *= -1;
        }

        $date = \DateTime::createFromFormat('ymd', $match[2]);
        $date->setTime(0, 0, 0);

        $balance = new Balance();
        $balance->setCurrency($match[3])
                ->setAmount($amount)
                ->setDate($date);

        return $balance;
    }

    /**
     * Get the opening balance
     *
     * @param mixed $text
     * @return void
     */
    protected function openingBalance($text)
    {
        if ($line = $this->getLine('60F', $text)) {
            return $this->balance($line);
        }
    }

    /**
     * Get the closing balance
     *
     * @param mixed $text
     * @return void
     */
    protected function closingBalance($text)
    {
        if ($line = $this->getLine('62F', $text)) {
            return $this->balance($line);
        }
    }

    /**
     * Create a Transaction from MT940 transaction text lines
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     * @return \Jejik\MT940\Transaction
     */
    protected function transaction(array $lines)
    {
        if (!preg_match('/(\d{6})(\d{4})?((?:C|D)R?)([0-9,]{1,15})/', $lines[0], $match)) {
            throw new \RuntimeException(sprintf('Could not parse transaction line "%s"', $lines[0]));
        }

        // Parse the amount
        $amount = (float) str_replace(',', '.', $match[4]);
        if (in_array($match[3], array('D', 'CR'))) {
            $amount *= -1;
        }

        // Parse dates
        $valueDate = \DateTime::createFromFormat('ymd', $match[1]);
        $valueDate->setTime(0,0,0);

        $bookDate = null;

        if ($match[2]) {
            $bookDate = \DateTime::createFromFormat('ymd', $valueDate->format('y') . $match[2]);

            // Handle bookdate in the next year. E.g. valueDate = dec 31, bookDate = jan 2
            if ((int) $bookDate->format('Y') < (int) $valueDate->format('Y')) {
                $bookDate->modify('+1 year');
            }
            $bookDate->setTime(0,0,0);
        }

        $description = isset($lines[1]) ? $lines[1] : null;
        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setContraAccount($this->contraAccount($lines))
                    ->setValueDate($valueDate)
                    ->setBookDate($bookDate)
                    ->setDescription($this->description($description));

        return $transaction;
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     * @return string|null
     */
    protected function contraAccount(array $lines)
    {
        return null;
    }

    /**
     * Process the description
     *
     * @param string $description
     * @return return
     */
    protected function description($description)
    {
        return $description;
    }

    /**
     * Test if the document can be read by the parser
     *
     * @param string $text
     * @return bool
     */
    abstract public function accept($text);
}
