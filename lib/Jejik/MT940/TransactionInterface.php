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
 * An MT940 transaction interface
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
interface TransactionInterface
{
    /**
     * Getter for contraAccount
     */
    public function getContraAccount(): ?AccountInterface;

    /**
     * Setter for contraAccount
     */
    public function setContraAccount(?AccountInterface $contraAccount = null): self;

    /**
     * Getter for amount
     */
    public function getAmount(): float;

    /**
     * Setter for amount
     */
    public function setAmount(float $amount): self;

    /**
     * Getter for description
     */
    public function getDescription(): ?string;

    /**
     * Setter for description
     */
    public function setDescription(?string $description): self;

    /**
     * Getter for valueDate
     */
    public function getValueDate(): ?\DateTime;

    /**
     * Setter for valueDate
     */
    public function setValueDate(?\DateTime $valueDate = null): self;

    /**
     * Getter for bookDate
     */
    public function getBookDate(): ?\DateTime;

    /**
     * Setter for bookDate
     */
    public function setBookDate(?\DateTime $bookDate = null): self;
    /**
     * Get Code for this transaction
     * @return null|string
     */
    public function getCode();

    /**
     * Set Code for this transaction
     * @param string $code
     * @return $this
     */
    public function setCode($code = null);

    /**
     * Get Ref for this transaction
     * @return null|string
     */
    public function getRef();

    /**
     * Set Ref for this transaction
     * @param string $ref
     * @return $this
     */
    public function setRef($ref = null);

    /**
     * Get BankRef for this transaction
     * @return null|string
     */
    public function getBankRef();

    /**
     * Set BankRef for this transaction
     * @param string $bankRef
     * @return $this
     */
    public function setBankRef($bankRef = null);

    /**
     * Get GVC for this transaction
     * @return null|string
     */
    public function getGVC();

    /**
     * Set GVC for this transaction
     * @param string $gvc
     * @return $this
     */
    public function setGVC($gvc = null);

    /**
     * Get extCode for this transaction
     * @return null|string
     */
    public function getExtCode();

    /**
     * Set ExtCode for this transaction
     * @param string $extCode
     * @return $this
     */
    public function setExtCode($extCode = null);

    /**
     * Get txText for this transaction
     * @return null|string
     */
    public function getTxText();

    /**
     * Set txText for this transaction
     * @param string $txText
     * @return $this
     */
    public function setTxText($txText = null);

    /**
     * Get primanota for this transaction
     * @return null|string
     */
    public function getPrimanota();

    /**
     * Set primanota for this transaction
     * @param string $primanota
     * @return $this
     */
    public function setPrimanota($primanota = null);

    /**
     * Get ERef for this transaction
     * @return null|string
     */
    public function getEref();

    /**
     * Set Eref for this transaction
     * @param string $eref
     * @return $this
     */
    public function setEref($eref = null);

    /**
     * Get BIC for this transaction
     * @return  null|string
     */
    public function getBIC();

    /**
     * Set BIC for this transaction
     * @param string $bic
     * @return $this
     */
    public function setBIC($bic = null);

    /**
     * Get IBAN for this transaction
     * @return  null|string
     */
    public function getIBAN();

    /**
     * Set IBAN for this transaction
     * @param string $iban
     * @return $this
     */
    public function setIBAN($iban = null);

    /**
     * Get Account Holder for this transaction
     * @return  null|string
     */
    public function getAccountHolder();

    /**
     * Set IBAN for this transaction
     * @param string $accountHolder
     * @return $this
     */
    public function setAccountHolder($accountHolder = null);

    /**
     * Get Kref for this transaction
     * @return null|string
     */
    public function getKref();

    /**
     * Set Kref for this transaction
     * @param string $kref
     * @return $this
     */
    public function setKref($kref = null);

    /**
     * Get Mref for this transaction
     * @return null|string
     */
    public function getMref();

    /**
     * Set Mref for this transaction
     * @param string $mref
     * @return $this
     */
    public function setMref($mref = null);

    /**
     * Get Cred for this transaction
     * @return null|string
     */
    public function getCred();

    /**
     * Set Cred for this transaction
     * @param string $cred
     * @return $this
     */
    public function setCred($cred = null);

    /**
     * Get Svwz for this transaction
     * @return null|string
     */
    public function getSvwz();

    /**
     * Set Svwz for this transaction
     * @param string $svwz
     * @return $this
     */
    public function setSvwz($svwz = null);
}
