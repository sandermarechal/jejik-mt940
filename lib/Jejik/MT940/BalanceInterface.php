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
 * Account balance interface
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
interface BalanceInterface
{
    /**
     * Getter for currency
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Setter for currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * Getter for amount
     *
     * @return float
     */
    public function getAmount();

    /**
     * Setter for amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Getter for date
     *
     * @return \DateTime
     */
    public function getDate();

    /**
     * Setter for date
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setDate($date);
}
