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
 * Account
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Account implements AccountInterface
{
    // Properties {{{
    /**
     * @var string Account currency
     */
    private $currency;

    /**
     * @var ?string Account number
     */
    private $number;

    /**
     * @var ?string Account holder name
     */
    private $name;

    // }}}

    // Getters and setters {{{

    /**
     * Get currency for this account
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Set currency for this account
     */
    public function setCurrency(string $currency): AccountInterface
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Getter for number
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * Setter for number
     */
    public function setNumber(?string $number): AccountInterface
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Getter for name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for name
     */
    public function setName(?string $name): AccountInterface
    {
        $this->name = $name;
        return $this;
    }

    // }}}
}
