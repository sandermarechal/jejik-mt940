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
 * An MT940 transaction interface
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
interface TransactionInterface
{
    /**
     * Getter for contraAccount
     *
     * @return string
     */
    public function getContraAccount();

    /**
     * Setter for contraAccount
     *
     * @param string $contraAccount
     * @return $this
     */
    public function setContraAccount($contraAccount);

    /**
     * Getter for amount
     *
     * @return float
     */
    public function getAmount();

    /**
     * Setter for amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Getter for description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Setter for description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Getter for valueDate
     *
     * @return \DateTime
     */
    public function getValueDate();

    /**
     * Setter for valueDate
     *
     * @param \DateTime $valueDate
     * @return $this
     */
    public function setValueDate($valueDate);

    /**
     * Getter for bookDate
     *
     * @return \DateTime
     */
    public function getBookDate();

    /**
     * Setter for bookDate
     *
     * @param \DateTime $bookDate
     * @return $this
     */
    public function setBookDate($bookDate);
}
