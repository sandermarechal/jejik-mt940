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

namespace Jejik\MT940;

/**
 * A single MT940 statement
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Statement implements StatementInterface
{
    // Properties {{{

    /**
     * @var string Statement sequence number
     */
    private $number;

    /**
     * @var string Account number
     */
    private $account;

    /**
     * @var \Jejik\MT940\Balance
     */
    private $openingBalance;

    /**
     * @var \Jejik\MT940\Balance
     */
    private $closingBalance;

    /**
     * @var array An array of \Jejik\MT940\Transaction
     */
    private $transactions = array();

    // }}}

    // Getters and setters {{{

    /**
     * Getter for number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Setter for number
     *
     * @param string $number
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Getter for account
     *
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Setter for account
     *
     * @param string $account
     * @return $this
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Getter for openingBalance
     *
     * @return \Jejik\MT940\Balance
     */
    public function getOpeningBalance()
    {
        return $this->openingBalance;
    }

    /**
     * Setter for openingBalance
     *
     * @param \Jejik\MT940\Balance $openingBalance
     * @return $this
     */
    public function setOpeningBalance(Balance $openingBalance = null)
    {
        $this->openingBalance = $openingBalance;
        return $this;
    }

    /**
     * Getter for closingBalance
     *
     * @return \Jejik\MT940\Balance
     */
    public function getClosingBalance()
    {
        return $this->closingBalance;
    }

    /**
     * Setter for closingBalance
     *
     * @param \Jejik\MT940\Balance $closingBalance
     * @return $this
     */
    public function setClosingBalance(Balance $closingBalance = null)
    {
        $this->closingBalance = $closingBalance;
        return $this;
    }

    /**
     * Getter for transactions
     *
     * @return array
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Add a transaction
     *
     * @param Transaction $transaction
     * @return $this
     */
    public function addTransaction(Transaction $transaction)
    {
        $this->transactions[] = $transaction;
        return $this;
    }

    // }}}
}
