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
     * @return void
     */
    public function setContraAccount(AccountInterface $contraAccount = null): void;

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
     * @return void
     */
    public function setAmount($amount): void;

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
     * @return void
     */
    public function setDescription($description): void;

    /**
     * Getter for valueDate
     *
     * @return \DateTime
     */
    public function getValueDate(): \DateTime;

    /**
     * Setter for valueDate
     *
     * @param \DateTime $valueDate
     *
     * @return void
     */
    public function setValueDate(\DateTime $valueDate = null): void;

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
     * @return void
     */
    public function setBookDate(\DateTime $bookDate = null): void;
}
