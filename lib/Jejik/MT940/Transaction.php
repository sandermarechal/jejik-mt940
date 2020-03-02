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
     */
    public function getContraAccount(): ?\Jejik\MT940\AccountInterface
    {
        return $this->contraAccount;
    }

    /**
     * Setter for contraAccount
     */
    public function setContraAccount(AccountInterface $contraAccount = null): Transaction
    {
        $this->contraAccount = $contraAccount;
        return $this;
    }

    /**
     * Getter for amount
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Setter for amount
     */
    public function setAmount(float $amount): Transaction
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Getter for description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Setter for description
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): Transaction
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Getter for valueDate
     */
    public function getValueDate(): ?\DateTime
    {
        return $this->valueDate;
    }

    /**
     * Setter for valueDate
     *
     * @param \DateTime|null $valueDate
     */
    public function setValueDate(\DateTime $valueDate = null): Transaction
    {
        $this->valueDate = $valueDate;
        return $this;
    }

    /**
     * Getter for bookDate
     */
    public function getBookDate(): ?\DateTime
    {
        return $this->bookDate;
    }

    /**
     * Setter for bookDate
     */
    public function setBookDate(\DateTime $bookDate = null): Transaction
    {
        $this->bookDate = $bookDate;
        return $this;
    }

    // }}}
}
