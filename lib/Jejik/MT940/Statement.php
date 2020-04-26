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
     * @var BalanceInterface
     */
    private $openingBalance;

    /**
     * @var BalanceInterface
     */
    private $closingBalance;

    /**
     * @var TransactionInterface[]
     */
    private $transactions = array();

    // }}}

    // Getters and setters {{{

    /**
     * Getter for number
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * Setter for number
     */
    public function setNumber(?string $number): StatementInterface
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Getter for account
     */
    public function getAccount(): ?AccountInterface
    {
        return $this->account;
    }

    /**
     * Setter for account
     */
    public function setAccount(?AccountInterface $account = null): StatementInterface
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Getter for openingBalance
     */
    public function getOpeningBalance(): ?BalanceInterface
    {
        return $this->openingBalance;
    }

    /**
     * Setter for openingBalance
     */
    public function setOpeningBalance(?BalanceInterface $openingBalance = null): StatementInterface
    {
        $this->openingBalance = $openingBalance;
        return $this;
    }

    /**
     * Getter for closingBalance
     */
    public function getClosingBalance(): ?BalanceInterface
    {
        return $this->closingBalance;
    }

    /**
     * Setter for closingBalance
     */
    public function setClosingBalance(?BalanceInterface $closingBalance = null): StatementInterface
    {
        $this->closingBalance = $closingBalance;
        return $this;
    }

    /**
     * Getter for transactions
     *
     * @return TransactionInterface[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * Add a transaction
     */
    public function addTransaction(TransactionInterface $transaction): ?StatementInterface
    {
        $this->transactions[] = $transaction;
        return $this;
    }
    // }}}
}
