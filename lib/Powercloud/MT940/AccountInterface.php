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
 * Account interface
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
interface AccountInterface
{
    /**
     * Get currency for this account
     */
    public function getCurrency(): string;

    /**
     * Set currency for this account
     */
    public function setCurrency(string $currency): self;

    /**
     * Getter for number
     */
    public function getNumber(): ?string;

    /**
     * Setter for number
     */
    public function setNumber(?string $number): self;

    /**
     * Getter for name
     */
    public function getName(): ?string;

    /**
     * Setter for name
     */
    public function setName(?string $name): self;
}
