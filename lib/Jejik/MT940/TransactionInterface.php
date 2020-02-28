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
     *
     * @return \Jejik\MT940\AccountInterface
     */
    public function getContraAccount(): \Jejik\MT940\AccountInterface;

    /**
     * Setter for contraAccount
     *
     * @param \Jejik\MT940\AccountInterface $contraAccount
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setContraAccount(AccountInterface $contraAccount = null): Transaction;

    /**
     * Getter for amount
     *
     * @return float
     */
    public function getAmount(): float;

    /**
     * Setter for amount
     *
     * @param float $amount
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setAmount($amount): Transaction;

    /**
     * Getter for description
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Setter for description
     *
     * @param string $description
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setDescription($description): Transaction;

    /**
     * Getter for valueDate
     *
     * @return \DateTime|null
     */
    public function getValueDate(): ?\DateTime;

    /**
     * Setter for valueDate
     *
     * @param \DateTime $valueDate
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setValueDate(\DateTime $valueDate = null): Transaction;

    /**
     * Getter for bookDate
     *
     * @return \DateTime
     */
    public function getBookDate(): \DateTime;

    /**
     * Setter for bookDate
     *
     * @param \DateTime $bookDate
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setBookDate(\DateTime $bookDate = null): Transaction;
}
