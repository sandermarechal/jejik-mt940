<?php

declare(strict_types=1);

/*
 * This file is part of the Powercloud\MT940 (a Fork of: Jejik\MT940) library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Powercloud\MT940;

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
     */
    public function setCurrency(string $currency): self;

    /**
     * Getter for amount
     */
    public function getAmount(): float;

    /**
     * Setter for amount
     */
    public function setAmount(float $amount): self;

    /**
     * Getter for date
     */
    public function getDate(): ?\DateTime;

    /**
     * Setter for date
     */
    public function setDate(?\DateTime $date): self;
}
