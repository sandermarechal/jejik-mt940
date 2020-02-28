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
    public function getContraAccount(): \Jejik\MT940\AccountInterface
    {
        return $this->contraAccount;
    }

    /**
     * Setter for contraAccount
     *
     * @param \Jejik\MT940\AccountInterface $contraAccount
     *
     * @return void
     */
    public function setContraAccount(AccountInterface $contraAccount = null): void
    {
        $this->contraAccount = $contraAccount;
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
     * @return void
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
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
     * @return void
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * Getter for valueDate
     *
     * @return \DateTime
     */
    public function getValueDate(): \DateTime
    {
        return $this->valueDate;
    }

    /**
     * Setter for valueDate
     *
     * @param \DateTime $valueDate
     *
     * @return void
     */
    public function setValueDate(\DateTime $valueDate = null): void
    {
        $this->valueDate = $valueDate;
    }

    /**
     * Getter for bookDate
     *
     * @return \DateTime
     */
    public function getBookDate(): \DateTime
    {
        return $this->bookDate;
    }

    /**
     * Setter for bookDate
     *
     * @param \DateTime $bookDate
     *
     * @return void
     */
    public function setBookDate(\DateTime $bookDate = null): void
    {
        $this->bookDate = $bookDate;
    }

    // }}}
}
