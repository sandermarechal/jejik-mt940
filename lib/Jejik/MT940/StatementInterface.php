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
 * Interface for a single MT940 statement
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
interface StatementInterface
{
    /**
     * Getter for number
     *
     * @return string
     */
    public function getNumber();

    /**
     * Setter for number
     *
     * @param string $number
     * @return $this
     */
    public function setNumber($number);

    /**
     * Getter for account
     *
     * @return string
     */
    public function getAccount();

    /**
     * Setter for account
     *
     * @param string $account
     * @return $this
     */
    public function setAccount($account);

    /**
     * Getter for openingBalance
     *
     * @return \Jejik\MT940\Balance
     */
    public function getOpeningBalance();

    /**
     * Setter for openingBalance
     *
     * @param \Jejik\MT940\Balance $openingBalance
     * @return $this
     */
    public function setOpeningBalance(Balance $openingBalance = null);

    /**
     * Getter for closingBalance
     *
     * @return \Jejik\MT940\Balance
     */
    public function getClosingBalance();

    /**
     * Setter for closingBalance
     *
     * @param \Jejik\MT940\Balance $closingBalance
     * @return $this
     */
    public function setClosingBalance(Balance $closingBalance = null);

    /**
     * Getter for transactions
     *
     * @return array
     */
    public function getTransactions();

    /**
     * Add a transaction
     *
     * @param Transaction $transaction
     * @return $this
     */
    public function addTransaction(Transaction $transaction);
}
