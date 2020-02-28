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
    public function getCurrency(): string;

    /**
     * Setter for currency
     *
     * @param string $currency
     *
     * @return \Jejik\MT940\Balance
     */
    public function setCurrency($currency): Balance;

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
     * @return \Jejik\MT940\Balance
     */
    public function setAmount($amount): Balance;

    /**
     * Getter for date
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime;

    /**
     * Setter for date
     *
     * @param \DateTime $date
     *
     * @return \Jejik\MT940\Balance
     */
    public function setDate($date): Balance;
}
