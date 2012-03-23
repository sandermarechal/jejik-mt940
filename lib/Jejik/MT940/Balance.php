<?php

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
class Balance
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
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
    
    /**
     * Setter for currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Getter for amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * Setter for amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Getter for date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Setter for date
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    // }}}
}
