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
 * An MT940 transaction
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Transaction implements TransactionInterface
{
    // Properties {{{

    /**
     * @var ?AccountInterface Contra account number
     */
    private $contraAccount;

    /**
     * @var float Transaction amount
     */
    private $amount = 0.0;

    /**
     * @var string Description
     */
    private $description;

    /**
     * @var \DateTime Date of the actual transaction
     */
    private $valueDate;

    /**
     * @var \DateTime Date the transaction was booked
     */
    private $bookDate;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $ref;

    /**
     * @var string
     */
    private $bankRef;

    /**
     * @var string
     */
    private $gvc;

    /**
     * @var string
     */
    private $txText;

    /**
     * @var string
     */
    private $primanota;

    /**
     * @var string
     */
    private $extCode;

    /**
     * @var string
     */
    private $eref;

    /**
     * @var string
     */
    private $bic;

    /**
     * @var string
     */
    private $iban;

    /**
     * @var string
     */
    private $accountHolder;

    /**
     * @var string
     */
    private $kref;

    /**
     * @var string
     */
    private $mref;

    /**
     * @var string
     */
    private $cred;

    /**
     * @var string
     */
    private $svwz;

    // }}}

    // Getters and setters {{{

    /**
     * Getter for contraAccount
     */
    public function getContraAccount(): ?AccountInterface
    {
        return $this->contraAccount;
    }

    /**
     * Setter for contraAccount
     */
    public function setContraAccount(?AccountInterface $contraAccount = null): TransactionInterface
    {
        $this->contraAccount = $contraAccount;
        return $this;
    }

    /**
     * Getter for amount
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Setter for amount
     */
    public function setAmount(float $amount): TransactionInterface
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Getter for description
     */
    public function getDescription(): ?string
    {
        return trim($this->description);
    }

    /**
     * Setter for description
     */
    public function setDescription(?string $description): TransactionInterface
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Getter for valueDate
     */
    public function getValueDate(): ?\DateTime
    {
        return $this->valueDate;
    }

    /**
     * Setter for valueDate
     */
    public function setValueDate(?\DateTime $valueDate = null): TransactionInterface
    {
        $this->valueDate = $valueDate;
        return $this;
    }

    /**
     * Getter for bookDate
     */
    public function getBookDate(): ?\DateTime
    {
        return $this->bookDate;
    }

    /**
     * Setter for bookDate
     */
    public function setBookDate(?\DateTime $bookDate = null): TransactionInterface
    {
        $this->bookDate = $bookDate;
        return $this;
    }

    /**
     * Get Code for this transaction
     * @return null|string
     */
    public function getCode() {
        return trim($this->code);
    }

    /**
     * Set Code for this transaction
     * @param string $code
     * @return $this
     */
    public function setCode($code = null) {
        $this->code = $code;
        return $this;
    }

    /**
     * Get Ref for this transaction
     * @return  null|string
     */
    public function getRef() {
        return trim($this->ref);
    }

    /**
     * Set Ref for this transaction
     * @param string $ref
     * @return $this
     */
    public function setRef($ref = null) {
        $this->ref = $ref;
        return $this;
    }

    /**
     * Get BankRef for this transaction
     * @return  null|string
     */
    public function getBankRef() {
        return trim($this->bankRef);
    }

    /**
     * Set BankRef for this transaction
     * @param string $bankRef
     * @return $this
     */
    public function setBankRef($bankRef = null) {
        $this->bankRef = $bankRef;
        return $this;
    }

    /**
     * Get GVC for this transaction
     * @return null|string
     */
    public function getGVC() {
        return trim($this->gvc);
    }

    /**
     * Set GVC for this transaction
     * @param string $gvc
     * @return $this
     */
    public function setGVC($gvc = null) {
        $this->gvc = $gvc;
        return $this;
    }

    /**
     * Get txText for this transaction
     * @return null|string
     */
    public function getTxText() {
        return trim($this->txText);
    }

    /**
     * Set txText for this transaction
     * @param string $txText
     * @return $this
     */
    public function setTxText($txText = null) {
        $this->txText = $txText;
        return $this;
    }

    /**
     * Get primanota for this transaction
     * @return null|string
     */
    public function getPrimanota() {
        return trim($this->primanota);
    }

    /**
     * Set primanota for this transaction
     * @param string $primanota
     * @return $this
     */
    public function setPrimanota($primanota = null) {
        $this->primanota = $primanota;
        return $this;
    }

    /**
     * Get extCode for this transaction
     * @return  null|string
     */
    public function getExtCode() {
        return trim($this->extCode);
    }

    /**
     * Set ExtCode for this transaction
     * @param string $extCode
     * @return $this
     */
    public function setExtCode($extCode = null) {
        $this->extCode = $extCode;
        return $this;
    }

    /**
     * Get ERef for this transaction
     * @return  null|string
     */
    public function getEref() {
        return trim($this->eref);
    }

    /**
     * Set Eref for this transaction
     * @param string $eref
     * @return $this
     */
    public function setEref($eref = null) {
        $this->eref = $eref;
        return $this;
    }

    /**
     * Get BIC for this transaction
     * @return  null|string
     */
    public function getBIC() {
        return trim($this->bic);
    }

    /**
     * Set BIC for this transaction
     * @param string $bic
     * @return $this
     */
    public function setBIC($bic = null) {
        $this->bic = $bic;
        return $this;
    }

    /**
     * Get IBAN for this transaction
     * @return  null|string
     */
    public function getIBAN() {
        return trim($this->iban);
    }

    /**
     * Set IBAN for this transaction
     * @param string $iban
     * @return $this
     */
    public function setIBAN($iban = null) {
        $this->iban = $iban;
        return $this;
    }

    /**
     * Get Account Holder for this transaction
     * @return  null|string
     */
    public function getAccountHolder() {
        return trim($this->accountHolder);
    }

    /**
     * Set IBAN for this transaction
     * @param string $accountHolder
     * @return $this
     */
    public function setAccountHolder($accountHolder = null) {
        $this->accountHolder = $accountHolder;
        return $this;
    }

    /**
     * Get Kref for this transaction
     * @return null|string
     */
    public function getKref() {
        return trim($this->kref);
    }

    /**
     * Set Kref for this transaction
     * @param string $kref
     * @return $this
     */
    public function setKref($kref = null) {
        $this->kref = $kref;
        return $this;
    }

    /**
     * Get Mref for this transaction
     * @return null|string
     */
    public function getMref() {
        return trim($this->mref);
    }

    /**
     * Set Mref for this transaction
     * @param string $mref
     * @return $this
     */
    public function setMref($mref = null) {
        $this->mref = $mref;
        return $this;
    }

    /**
     * Get Cred for this transaction
     * @return null|string
     */
    public function getCred() {
        return trim($this->cred);
    }

    /**
     * Set Cred for this transaction
     * @param string $cred
     * @return $this
     */
    public function setCred($cred = null) {
        $this->cred = $cred;
        return $this;
    }

    /**
     * Get Svwz for this transaction
     * @return null|string
     */
    public function getSvwz() {
        return trim($this->svwz);
    }

    /**
     * Set Svwz for this transaction
     * @param string $svwz
     * @return $this
     */
    public function setSvwz($svwz = null) {
        $this->svwz = $svwz;
        return $this;
    }

    // }}}
}
