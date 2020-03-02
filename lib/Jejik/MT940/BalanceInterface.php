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
     */
    public function getCurrency(): string;

    /**
     * Setter for currency
     *
     * @param string $currency
     */
    public function setCurrency(string $currency): Balance;

    /**
     * Getter for amount
     */
    public function getAmount(): float;

    /**
     * Setter for amount
     *
     * @param float $amount
     */
    public function setAmount(float $amount): Balance;

    /**
     * Getter for date
     */
    public function getDate(): \DateTime;

    /**
     * Setter for date
     *
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): Balance;
}
