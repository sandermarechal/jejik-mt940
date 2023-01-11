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

use Jejik\MT940\AccountInterface;
use Jejik\MT940\Balance;
use Jejik\MT940\BalanceInterface;
use Jejik\MT940\StatementInterface;
use Jejik\MT940\Reader;
use Jejik\MT940\Statement;
use Jejik\MT940\TransactionInterface;

/**
 * Base MT940 parser
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
abstract class AbstractParser
{
    /**
     * Reference to the MT940 reader
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Constructor
     *
     * @param Reader $reader Reference to the MT940 reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Check whether provided bank statement contains CRLF or not
     * - if not then replace existing with CRLF (required for this parser)
     * @param $text
     */
    public function checkCRLF(&$text)
    {
        $text = preg_replace("#(\r\n|\r|\n)#", "\r\n", $text);
    }

    /**
     * Get the transaction reference number of an MT940 document.
     * It is the :20: field at the beginning of each MT940 bankaccount statement.
     *
     * @param string $text The MT940 document
     * @return string The transaction reference number
     */
    public function getTransactionReferenceNumber(string $text): string
    {
        $startpos = strpos($text, ':20:');
        if ($startpos === false) {
            throw new \RuntimeException('Not an MT940 statement');
        }
        $endpos = strpos($text, "\r\n", $startpos);
        if ($endpos === false) {
            throw new \RuntimeException('Not an MT940 statement');
        }
        return substr($text, $startpos + 4, $endpos - $startpos - 4);
    }

    /**
     * Check whether BLZ for provided bank statement text is allowed or not
     * @param string $text
     * @return bool
     */
    public function isBLZAllowed($text): bool
    {
        $this->checkCRLF($text);
        $account = $this->getLine('25', $text);
        if (!is_null($account)) {
            $accountExploded = explode('/', $account);
            if (isset($accountExploded[0]) && in_array($accountExploded[0], $this->getAllowedBLZ())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get an array of allowed BLZ for this bank
     */
    abstract public function getAllowedBLZ(): array;

    /**
     * Parse an MT940 document
     *
     * @param string $text Full document text
     *
     * @return Statement[]
     * @throws \Exception
     */
    public function parse(string $text): array
    {
        $this->checkCRLF($text);
        $statements = [];
        foreach ($this->splitStatements($text) as $chunk) {
            if ($statement = $this->statement($chunk)) {
                $statements[] = $statement;
            }
        }

        return $statements;
    }

    /**
     * Get the contents of an MT940 line
     *
     * The contents may be several lines long (e.g. :86: descriptions)
     *
     * @param string $id The line ID (e.g. "20"). Can be a regular
     *                         expression (e.g. "60F|60M")
     * @param string $text The text to search
     * @param int $offset The offset to start looking
     * @param int|null $position Starting position of the found line
     * @param int|null $length Length of the found line (before trimming),
     *                         including EOL
     * @return string|null
     */
    protected function getLine(
        string $id,
        string $text,
        int $offset = 0,
        int &$position = null,
        int &$length = null
    ): ?string {
        $pcre = '/(?:^|\r\n)\:(' . $id . ')\:'   // ":<id>:" at the start of a line
            . '(.+)'                           // Contents of the line
            . '(:?$|\r\n\:[[:alnum:]]{2,3}\:)' // End of the text or next ":<id>:"
            . '/Us';                           // Ungreedy matching

        $substring = substr($text, $offset);
        if ($substring !== false) {
            // Offset manually, so the start of the offset can match ^
            if (preg_match($pcre, $substring, $match, PREG_OFFSET_CAPTURE)) {
                $position = $offset + $match[1][1] - 1;
                $length = strlen($match[2][0]);

                return rtrim($match[2][0]);
            }
        }

        return null;
    }

    /**
     * Get the contents of an MT940 line
     *
     * The contents may be several lines long (e.g. :86: descriptions)
     *
     * @param string $text The text to search
     * @return array|null
     */
    protected function getTransactionLines(string $text): ?array
    {
        $amountLine = [];
        $pcre = '/(?:^|\r\n)\:(?:61)\:(.+)(?::?$|\r\n\:[[:alnum:]]{2,3}\:)/Us';

        if (preg_match_all($pcre, $text, $match)) {
            $amountLine = $match;
        }

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

    /**
     * Split the text into separate statement chunks
     *
     * @param string $text Full document text
     *
     * @return string[] Array of statement texts
     * @throws \RuntimeException if the statementDelimiter is not set
     */
    protected function splitStatements(string $text): array
    {
        $chunks = preg_split('/^:20:/m', $text, -1);
        $chunks = array_filter(array_map('trim', array_slice($chunks, 1)));

        // Re-add the :20: at the beginning
        return array_map(function ($statement) {
            return ':20:' . $statement;
        }, $chunks);
    }

    /**
     * Split transactions and their descriptions from the statement text
     *
     * Returns a nexted array of transaction lines. The transaction line text
     * is at offset 0 and the description line text (if any) at offset 1.
     *
     * @param string $text Statement text
     *
     * @return array Nested array of transaction and description lines
     */
    protected function splitTransactions(string $text): array
    {
        $transactionLines = $this->getTransactionLines($text);
        return $transactionLines ?? [];
    }

    /**
     * Parse a statement chunk
     *
     * @param string $text Statement text
     *
     * @throws \Exception
     */
    protected function statement(string $text): ?Statement
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
     */
    protected function statementHeader(string $text): void
    {
    }

    /**
     * Parse a statement body
     *
     * @param string $text Statement body text
     *
     * @throws \Exception
     */
    protected function statementBody(string $text): ?Statement
    {
        $accountNumber = $this->accountNumber($text);
        $accountCurrency = $this->accountCurrency($text);
        $account = $this->reader->createAccount($accountNumber);

        if (!($account instanceof AccountInterface)) {
            return null;
        }

        $account->setNumber($accountNumber);
        $account->setCurrency($accountCurrency);
        $number = $this->statementNumber($text);
        /** @var Statement $statement */
        $statement = $this->reader->createStatement($account, $number);

        if (!($statement instanceof StatementInterface)) {
            return null;
        }

        $statement
            ->setAccount($account)
            ->setNumber($this->statementNumber($text))
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
     */
    protected function statementNumber(string $text): ?string
    {
        $number = $this->getLine('28|28C', $text);
        if (!is_null($number)) {
            return $number;
        }

        return null;
    }

    /**
     * Parse an account number
     *
     * @param string $text Statement body text
     */
    protected function accountNumber(string $text): ?string
    {
        if ($account = $this->getLine('25', $text)) {
            return ltrim($account, '0');
        }

        return null;
    }

    /**
     * Parse account currency
     */
    protected function accountCurrency($text): ?string
    {
        $accountNumber = $this->accountNumber($text);
        if ($accountNumber === null) {
            return null;
        }
        // last 3 characters comprises its ISO currency code
        $currency = substr($accountNumber, strlen($accountNumber) - 3, 3);
        $pcreCurrency = '/([A-Z]{3})$/';
        if (!preg_match($pcreCurrency, $currency)) {
            // try it from 60F
            if ($line60F = $this->getLine('60F', $text)) {
                $pcreCurrency = '/(C|D)(\d{6})([A-Z]{3})([0-9,]{1,15})/';
                preg_match($pcreCurrency, $text, $match);
                if (isset($match[3])) {
                    return $match[3];
                }
                return null;
            }
        }
        return $currency;
    }

    /**
     * Create a Balance object from an MT940  balance line
     */
    protected function balance(BalanceInterface $balance, string $text): BalanceInterface
    {
        if (!preg_match('/(C|D)(\d{6})([A-Z]{3})([0-9,]{1,15})/', $text, $match)) {
            throw new \RuntimeException(sprintf('Cannot parse balance: "%s"', $text));
        }

        $amount = (float)str_replace(',', '.', $match[4]);
        if ($match[1] === 'D') {
            $amount *= -1;
        }

        $date = \DateTime::createFromFormat('ymd', $match[2]);
        $date->setTime(0, 0, 0);

        $balance
            ->setCurrency($match[3])
            ->setAmount($amount)
            ->setDate($date);

        return $balance;
    }

    /**
     * Get the opening balance
     */
    protected function openingBalance(string $text): ?Balance
    {
        if ($line = $this->getLine('60F|60M', $text)) {
            return $this->balance($this->reader->createOpeningBalance(), $line);
        }

        return null;
    }

    /**
     * Get the closing balance
     */
    protected function closingBalance(string $text): ?Balance
    {
        if ($line = $this->getLine('62F|62M', $text)) {
            return $this->balance($this->reader->createClosingBalance(), $line);
        }

        return null;
    }

    /**
     * Create a Transaction from MT940 transaction text lines
     *
     * @param array $lines The transaction text at offset 0 and the description
     *                     at offset 1
     *
     * @throws \Exception
     */
    protected function transaction(array $lines): TransactionInterface
    {
        if (!preg_match('/(\d{6})(\d{4})?((?:C|D|RD|RC)R?)([0-9,]{1,15})/', $lines[0], $match)) {
            throw new \RuntimeException(sprintf('Could not parse transaction line "%s"', $lines[0]));
        }

        // Parse the amount
        $amount = (float)str_replace(',', '.', $match[4]);
        if (in_array($match[3], array('D', 'DR','RC','RCR'))) {
            $amount *= -1;
        }

        // Parse dates
        $valueDate = \DateTime::createFromFormat('ymd', $match[1]);
        $valueDate->setTime(0, 0, 0);

        $bookDate = null;

        if ($match[2]) {
            // current|same year as valueDate
            $bookDate_sameYear = \DateTime::createFromFormat('ymd', $valueDate->format('y') . $match[2]);
            $bookDate_sameYear->setTime(0, 0, 0);

            /* consider proper year -- $valueDate = '160104'(YYMMTT) & $bookDate = '1228'(MMTT) */
            // previous year bookDate
            $bookDate_previousYear = clone($bookDate_sameYear);
            $bookDate_previousYear->modify('-1 year');

            // next year bookDate
            $bookDate_nextYear = clone($bookDate_sameYear);
            $bookDate_nextYear->modify('+1 year');

            // bookDate collection
            $bookDateCollection = [];

            // previous year diff
            $bookDate_previousYear_diff = $valueDate->diff($bookDate_previousYear);
            $bookDateCollection[$bookDate_previousYear_diff->days] = $bookDate_previousYear;

            // current|same year as valueDate diff
            $bookDate_sameYear_diff = $valueDate->diff($bookDate_sameYear);
            $bookDateCollection[$bookDate_sameYear_diff->days] = $bookDate_sameYear;

            // next year diff
            $bookDate_nextYear_diff = $valueDate->diff($bookDate_nextYear);
            $bookDateCollection[$bookDate_nextYear_diff->days] = $bookDate_nextYear;

            // get the min from these diffs
            $bookDate = $bookDateCollection[min(array_keys($bookDateCollection))];
        }

        $description = isset($lines[1]) ? $lines[1] : null;
        $transaction = $this->reader->createTransaction();
        $transaction
            ->setAmount($amount)
            ->setContraAccount($this->contraAccount($lines))
            ->setValueDate($valueDate)
            ->setBookDate($bookDate)
            ->setCode($this->code($lines))
            ->setRef($this->ref($lines))
            ->setBankRef($this->bankRef($lines))
            ->setSupplementaryDetails($this->supplementaryDetails($lines))
            ->setGVC($this->gvc($lines))
            ->setTxText($this->txText($lines))
            ->setPrimanota($this->primanota($lines))
            ->setExtCode($this->extCode($lines))
            ->setEref($this->eref($lines))
            ->setBIC($this->bic($lines))
            ->setIBAN($this->iban($lines))
            ->setAccountHolder($this->accountHolder($lines))
            ->setKref($this->kref($lines))
            ->setMref($this->mref($lines))
            ->setCred($this->cred($lines))
            ->setSvwz($this->svwz($lines))
            ->setPurp($this->purp($lines))
            ->setDebt($this->debt($lines))
            ->setCoam($this->coam($lines))
            ->setOamt($this->oamt($lines))
            ->setAbwa($this->abwa($lines))
            ->setAbwe($this->abwe($lines))
            ->setDescription($this->description($description));

        return $transaction;
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description
     *                     at offset 1
     */
    protected function contraAccount(array $lines): ?AccountInterface
    {
        $number = $this->contraAccountNumber($lines);
        $name   = $this->contraAccountName($lines);

        if ($name || $number) {
            $contraAccount = $this->reader->createContraAccount($number);
            $contraAccount
                ->setNumber($number)
                ->setName($name);

            return $contraAccount;
        }

        return null;
    }

    /**
     * Get the contra account number from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description
     *                     at offset 1
     */
    protected function contraAccountNumber(array $lines): ?string
    {
        return null;
    }

    /**
     * Get the contra account holder name from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description
     *                     at offset 1
     */
    protected function contraAccountName(array $lines): ?string
    {
        return null;
    }

    /**
     * Process the description
     */
    protected function description(?string $description): ?string
    {
        if ($description === null) {
            return null;
        }
        //return implode('', array_map('trim', explode("\r\n", $description)));
        return implode("\r\n", explode("\r\n", $description));
    }

    /**
     * Test if the document can be read by the parser
     */
    abstract public function accept(string $text): bool;

    /**
     * Parse GVC for provided transaction lines
     */
    protected function gvc(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse code for provided transaction lines
     */
    protected function code(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse ref for provided transaction lines
     */
    protected function ref(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse bankRef for provided transaction lines
     */
    protected function bankRef(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse supplementary details
     */
    protected function supplementaryDetails(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse txText for provided transaction lines
     */
    protected function txText(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse primanota for provided transaction lines
     */
    protected function primanota(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse extCode for provided transaction lines
     */
    protected function extCode(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse eref for provided transaction lines
     */
    protected function eref(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse bic for provided transaction lines
     */
    protected function bic(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse iban for provided transaction lines
     */
    protected function iban(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse accountHolder for provided transaction lines
     */
    protected function accountHolder(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse kref for provided transaction lines
     */
    protected function kref(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse mref for provided transaction lines
     */
    protected function mref(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse cred for provided transaction lines
     */
    protected function cred(array $lines): ?string
    {
        return null;
    }

    /**
     * Parse svwz for provided transaction lines
     */
    protected function svwz(array $lines): ?string
    {
        return null;
    }
    
    /**
     * Parse purp for provided transaction lines
     */
    protected function purp(array $lines): ?string
    {
        return null;
    }
    
    /**
     * Parse debt for provided transaction lines
     */
    protected function debt(array $lines): ?string
    {
        return null;
    }
    
    /**
     * Parse coam for provided transaction lines
     */
    protected function coam(array $lines): ?string
    {
        return null;
    }
    
    /**
     * Parse oamt for provided transaction lines
     */
    protected function oamt(array $lines): ?string
    {
        return null;
    }
    
    /**
     * Parse abwa for provided transaction lines
     */
    protected function abwa(array $lines): ?string
    {
        return null;
    }
    
    /**
     * Parse abwe for provided transaction lines
     */
    protected function abwe(array $lines): ?string
    {
        return null;
    }
}
