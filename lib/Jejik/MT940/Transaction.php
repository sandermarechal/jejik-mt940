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
 * An MT940 transaction
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Transaction implements TransactionInterface
{
    // Properties {{{

    /**
     * @var \Jejik\MT940\AccountInterface Contra account number
     */
    private $contraAccount;

    /**
     * @var float Transaction amount
     */
    private $amount;

    /**
     * @var string Description
     */
    private $description;

    /**
     * @var \DateTime Date of the actual transaction
     */
    private $valueDate;

    /**
     * @var \DateTime Date the transaction was booked
     */
    private $bookDate;

    // }}}

    // Getters and setters {{{

    /**
     * Getter for contraAccount
     *
     * @return \Jejik\MT940\AccountInterface
     */
    public function getContraAccount(): ?\Jejik\MT940\AccountInterface
    {
        return $this->contraAccount;
    }

    /**
     * Setter for contraAccount
     *
     * @param \Jejik\MT940\AccountInterface $contraAccount
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setContraAccount(AccountInterface $contraAccount = null): Transaction
    {
        $this->contraAccount = $contraAccount;
        return $this;
    }

    /**
     * Getter for amount
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Setter for amount
     *
     * @param float $amount
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setAmount($amount): Transaction
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Getter for description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Setter for description
     *
     * @param string $description
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setDescription($description): Transaction
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Getter for valueDate
     *
     * @return \DateTime|null
     */
    public function getValueDate(): ?\DateTime
    {
        return $this->valueDate;
    }

    /**
     * Setter for valueDate
     *
     * @param \DateTime $valueDate
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setValueDate(\DateTime $valueDate = null): Transaction
    {
        $this->valueDate = $valueDate;
        return $this;
    }

    /**
     * Getter for bookDate
     *
     * @return \DateTime
     */
    public function getBookDate(): ?\DateTime
    {
        return $this->bookDate;
    }

    /**
     * Setter for bookDate
     *
     * @param \DateTime $bookDate
     *
     * @return \Jejik\MT940\Transaction
     */
    public function setBookDate(\DateTime $bookDate = null): Transaction
    {
        $this->bookDate = $bookDate;
        return $this;
    }

    // }}}
}
