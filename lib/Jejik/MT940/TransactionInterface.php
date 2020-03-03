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
 * An MT940 transaction interface
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
interface TransactionInterface
{
    /**
     * Getter for contraAccount
     */
    public function getContraAccount(): ?AccountInterface;

    /**
     * Setter for contraAccount
     */
    public function setContraAccount(AccountInterface $contraAccount = null): Transaction;

    /**
     * Getter for amount
     */
    public function getAmount(): float;

    /**
     * Setter for amount
     */
    public function setAmount(float $amount): Transaction;

    /**
     * Getter for description
     */
    public function getDescription(): string;

    /**
     * Setter for description
     */
    public function setDescription(string $description): Transaction;

    /**
     * Getter for valueDate
     */
    public function getValueDate(): ?\DateTime;

    /**
     * Setter for valueDate
     */
    public function setValueDate(\DateTime $valueDate = null): Transaction;

    /**
     * Getter for bookDate
     */
    public function getBookDate(): ?\DateTime;

    /**
     * Setter for bookDate
     */
    public function setBookDate(\DateTime $bookDate = null): Transaction;
}
