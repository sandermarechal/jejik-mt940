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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Setter for number
     *
     * @param string $number
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Getter for name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter for name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    // }}}
}
