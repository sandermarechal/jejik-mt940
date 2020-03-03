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
     */
    public function getNumber(): ?string;

    /**
     * Setter for number
     */
    public function setNumber(string $number): Statement;

    /**
     * Getter for account
     */
    public function getAccount(): \Jejik\MT940\AccountInterface;

    /**
     * Setter for account
     */
    public function setAccount(AccountInterface $account = null): Statement;

    /**
     * Getter for openingBalance
     */
    public function getOpeningBalance(): \Jejik\MT940\BalanceInterface;

    /**
     * Setter for openingBalance
     */
    public function setOpeningBalance(BalanceInterface $openingBalance = null): Statement;

    /**
     * Getter for closingBalance
     */
    public function getClosingBalance(): \Jejik\MT940\BalanceInterface;

    /**
     * Setter for closingBalance
     */
    public function setClosingBalance(BalanceInterface $closingBalance = null): Statement;

    /**
     * Getter for transactions
     *
     * @return array of \Jejik\MT940\TransactionInterface
     */
    public function getTransactions(): array;

    /**
     * Add a transaction
     */
    public function addTransaction(TransactionInterface $transaction): void;
}
