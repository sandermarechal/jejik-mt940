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
     * @var string Account number
     */
    private $number;

    /**
     * @var string Account holder name
     */
    private $name;

    // }}}

    // Getters and setters {{{

    /**
     * Getter for number
     *
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Setter for number
     *
     * @param string $number
     *
     * @return void
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * Getter for name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setter for name
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    // }}}
}
