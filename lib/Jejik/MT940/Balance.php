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
 * Account balance
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Balance implements BalanceInterface
{
    // Properties {{{

    /**
     * @var string ISO 4217 currency code
     */
    private $currency;

    /**
     * @var float amount
     */
    private $amount;

    /**
     * @var \DateTime
     */
    private $date;

    // }}}

    // Getters and setters {{{

    /**
     * Getter for currency
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Setter for currency
     */
    public function setCurrency(string $currency): Balance
    {
        $this->currency = $currency;
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
    public function setAmount(float $amount): Balance
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Getter for date
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * Setter for date
     */
    public function setDate(\DateTime $date): Balance
    {
        $this->date = $date;
        return $this;
    }

    // }}}
}
