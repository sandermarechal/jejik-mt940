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
 * Account interface
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
interface AccountInterface
{
    /**
     * Getter for number
     */
    public function getNumber(): ?string;

    /**
     * Setter for number
     */
    public function setNumber(string $number): Account;

    /**
     * Getter for name
     */
    public function getName(): string;

    /**
     * Setter for name
     */
    public function setName(string $name): Account;
}
