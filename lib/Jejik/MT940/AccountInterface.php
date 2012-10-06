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
 * Account interface
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
interface AccountInterface
{
    /**
     * Getter for number
     *
     * @return string
     */
    public function getNumber();

    /**
     * Setter for number
     *
     * @param string $number
     * @return $this
     */
    public function setNumber($number);

    /**
     * Getter for name
     *
     * @return string
     */
    public function getName();

    /**
     * Setter for name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);
}
