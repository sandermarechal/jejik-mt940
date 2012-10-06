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
     * @return \Jejik\MT940\AccountInterface
     */
    public function getAccount();

    /**
     * Setter for account
     *
     * @param \Jejik\MT940\AccountInterface $account
     * @return $this
     */
    public function setAccount(AccountInterface $account = null);

    /**
     * Getter for openingBalance
     *
     * @return \Jejik\MT940\BalanceInterface
     */
    public function getOpeningBalance();

    /**
     * Setter for openingBalance
     *
     * @param \Jejik\MT940\BalanceInterface $openingBalance
     * @return $this
     */
    public function setOpeningBalance(BalanceInterface $openingBalance = null);

    /**
     * Getter for closingBalance
     *
     * @return \Jejik\MT940\BalanceInterface
     */
    public function getClosingBalance();

    /**
     * Setter for closingBalance
     *
     * @param \Jejik\MT940\Balance $closingBalance
     * @return $this
     */
    public function setClosingBalance(BalanceInterface $closingBalance = null);

    /**
     * Getter for transactions
     *
     * @return array of \Jejik\MT940\TransactionInterface
     */
    public function getTransactions();

    /**
     * Add a transaction
     *
     * @param TransactionInterface $transaction
     * @return $this
     */
    public function addTransaction(TransactionInterface $transaction);
}
