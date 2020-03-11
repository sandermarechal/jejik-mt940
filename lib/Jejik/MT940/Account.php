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
 * Account
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Account implements AccountInterface
{
    // Properties {{{

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
     * Getter for number
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * Setter for number
     */
    public function setNumber(?string $number): self
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
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // }}}
}
