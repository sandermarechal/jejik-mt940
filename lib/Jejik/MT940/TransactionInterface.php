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
     */
    public function setCode(string $code = null): self;

    /**
     * Get Ref for this transaction
     */
    public function getRef(): ?string;

    /**
     * Set Ref for this transaction
     */
    public function setRef(string $ref = null): TransactionInterface;

    /**
     * Get BankRef for this transaction
     */
    public function getBankRef(): ?string;

    /**
     * Set BankRef for this transaction
     */
    public function setBankRef(string $bankRef = null): TransactionInterface;

    /**
     * Set supplementary details
     */
    public function setSupplementaryDetails(?string $supplementaryDetails): TransactionInterface;

    /**
     * Get supplementary details
     */
    public function getSupplementaryDetails(): ?string;

    /**
     * Get GVC for this transaction
     */
    public function getGVC(): ?string;

    /**
     * Set GVC for this transaction
     */
    public function setGVC(string $gvc = null): TransactionInterface;

    /**
     * Get extCode for this transaction
     */
    public function getExtCode(): ?string;

    /**
     * Set ExtCode for this transaction
     */
    public function setExtCode(string $extCode = null): TransactionInterface;

    /**
     * Get txText for this transaction
     */
    public function getTxText(): ?string;

    /**
     * Set txText for this transaction
     */
    public function setTxText(string $txText = null): TransactionInterface;

    /**
     * Get primanota for this transaction
     */
    public function getPrimanota(): ?string;

    /**
     * Set primanota for this transaction
     */
    public function setPrimanota(string $primanota = null): TransactionInterface;

    /**
     * Get ERef for this transaction
     */
    public function getEref(): ?string;

    /**
     * Set Eref for this transaction
     * @param string $eref
     * @return $this
     */
    public function setEref(string $eref = null): TransactionInterface;

    /**
     * Get BIC for this transaction
     */
    public function getBIC(): ?string;

    /**
     * Set BIC for this transaction
     */
    public function setBIC(string $bic = null): TransactionInterface;

    /**
     * Get IBAN for this transaction
     */
    public function getIBAN(): ?string;

    /**
     * Set IBAN for this transaction
     * @param string $iban
     * @return $this
     */
    public function setIBAN(string $iban = null): TransactionInterface;

    /**
     * Get Account Holder for this transaction
     */
    public function getAccountHolder(): ?string;

    /**
     * Set IBAN for this transaction
     */
    public function setAccountHolder(string $accountHolder = null): TransactionInterface;

    /**
     * Get Kref for this transaction
     */
    public function getKref(): ?string;

    /**
     * Set Kref for this transaction
     */
    public function setKref(string $kref = null): TransactionInterface;

    /**
     * Get Mref for this transaction
     */
    public function getMref(): ?string;

    /**
     * Set Mref for this transaction
     */
    public function setMref(string $mref = null): TransactionInterface;

    /**
     * Get Cred for this transaction
     */
    public function getCred(): ?string;

    /**
     * Set Cred for this transaction
     */
    public function setCred(string $cred = null): TransactionInterface;

    /**
     * Get Svwz for this transaction
     */
    public function getSvwz(): ?string;

    /**
     * Set Svwz for this transaction
     */
    public function setSvwz(string $svwz = null): TransactionInterface;
}
