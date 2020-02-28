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
     * @var AccountInterface Account
     */
    private $account;

    /**
     * @var \Jejik\MT940\BalanceInterface
     */
    private $openingBalance;

    /**
     * @var \Jejik\MT940\BalanceInterface
     */
    private $closingBalance;

    /**
     * @var \Jejik\MT940\TransactionInterface[]
     */
    private $transactions = array();

    // }}}

    // Getters and setters {{{

    /**
     * Getter for number
     *
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Setter for number
     *
     * @param string $number
     *
     * @return void
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * Getter for account
     *
     * @return \Jejik\MT940\AccountInterface
     */
    public function getAccount(): \Jejik\MT940\AccountInterface
    {
        return $this->account;
    }

    /**
     * Setter for account
     *
     * @param \Jejik\MT940\AccountInterface $account
     *
     * @return void
     */
    public function setAccount(AccountInterface $account = null): void
    {
        $this->account = $account;
    }

    /**
     * Getter for openingBalance
     *
     * @return \Jejik\MT940\BalanceInterface
     */
    public function getOpeningBalance(): \Jejik\MT940\BalanceInterface
    {
        return $this->openingBalance;
    }

    /**
     * Setter for openingBalance
     *
     * @param \Jejik\MT940\BalanceInterface $openingBalance
     *
     * @return void
     */
    public function setOpeningBalance(BalanceInterface $openingBalance = null): void
    {
        $this->openingBalance = $openingBalance;
    }

    /**
     * Getter for closingBalance
     *
     * @return \Jejik\MT940\BalanceInterface
     */
    public function getClosingBalance(): \Jejik\MT940\BalanceInterface
    {
        return $this->closingBalance;
    }

    /**
     * Setter for closingBalance
     *
     * @param \Jejik\MT940\BalanceInterface $closingBalance
     *
     * @return void
     */
    public function setClosingBalance(BalanceInterface $closingBalance = null): void
    {
        $this->closingBalance = $closingBalance;
    }

    /**
     * Getter for transactions
     *
     * @return \Jejik\MT940\TransactionInterface[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * Add a transaction
     *
     * @param TransactionInterface $transaction
     *
     * @return void
     */
    public function addTransaction(TransactionInterface $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    // }}}
}
