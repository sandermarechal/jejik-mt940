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
     */
    public function getCode(): ?string
    {
        return ($this->code !== null) ? trim($this->code) : null;
    }

    /**
     * Set Code for this transaction
     * @param string $code
     * @return $this
     */
    public function setCode(string $code = null): TransactionInterface
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get Ref for this transaction
     */
    public function getRef(): ?string
    {
        return ($this->ref !== null) ? trim($this->ref) : null;
    }

    /**
     * Set Ref for this transaction
     */
    public function setRef(string $ref = null): TransactionInterface
    {
        $this->ref = $ref;
        return $this;
    }

    /**
     * Get BankRef for this transaction
     */
    public function getBankRef(): ?string
    {
        return ($this->bankRef !== null) ? trim($this->bankRef) : null;
    }

    /**
     * Set BankRef for this transaction
     */
    public function setBankRef(string $bankRef = null): TransactionInterface
    {
        $this->bankRef = $bankRef;
        return $this;
    }

    /**
     * Get GVC for this transaction
     */
    public function getGVC(): ?string
    {
        return ($this->gvc !== null) ? trim($this->gvc) : null;
    }

    /**
     * Set GVC for this transaction
     */
    public function setGVC(string $gvc = null): TransactionInterface
    {
        $this->gvc = $gvc;
        return $this;
    }

    /**
     * Get txText for this transaction
     */
    public function getTxText(): ?string
    {
        return ($this->txText !== null) ? trim($this->txText): null;
    }

    /**
     * Set txText for this transaction
     */
    public function setTxText(string $txText = null): TransactionInterface
    {
        $this->txText = $txText;
        return $this;
    }

    /**
     * Get primanota for this transaction
     */
    public function getPrimanota(): ?string
    {
        return ($this->primanota !== null) ? trim($this->primanota) : null;
    }

    /**
     * Set primanota for this transaction
     */
    public function setPrimanota(string $primanota = null): TransactionInterface
    {
        $this->primanota = $primanota;
        return $this;
    }

    /**
     * Get extCode for this transaction
     */
    public function getExtCode(): ?string
    {
        return ($this->extCode !== null) ? trim($this->extCode) : null;
    }

    /**
     * Set ExtCode for this transaction
     */
    public function setExtCode(string $extCode = null): TransactionInterface
    {
        $this->extCode = $extCode;
        return $this;
    }

    /**
     * Get ERef for this transaction
     * @return  null|string
     */
    public function getEref(): ?string
    {
        return ($this->eref !== null) ? trim($this->eref) : null;
    }

    /**
     * Set Eref for this transaction
     */
    public function setEref(string $eref = null): TransactionInterface
    {
        $this->eref = $eref;
        return $this;
    }

    /**
     * Get BIC for this transaction
     */
    public function getBIC(): ?string
    {
        return trim($this->bic);
    }

    /**
     * Set BIC for this transaction
     */
    public function setBIC(string $bic = null): TransactionInterface
    {
        $this->bic = $bic;
        return $this;
    }

    /**
     * Get IBAN for this transaction
     */
    public function getIBAN(): ?string
    {
        return trim($this->iban);
    }

    /**
     * Set IBAN for this transaction
     */
    public function setIBAN(string $iban = null): TransactionInterface
    {
        $this->iban = $iban;
        return $this;
    }

    /**
     * Get Account Holder for this transaction
     */
    public function getAccountHolder(): ?string
    {
        return ($this->accountHolder !== null) ? trim($this->accountHolder) : null;
    }

    /**
     * Set IBAN for this transaction
     */
    public function setAccountHolder(string $accountHolder = null): TransactionInterface
    {
        $this->accountHolder = $accountHolder;
        return $this;
    }

    /**
     * Get Kref for this transaction
     */
    public function getKref(): ?string
    {
        return ($this->kref !== null) ? trim($this->kref) : null;
    }

    /**
     * Set Kref for this transaction
     */
    public function setKref(string $kref = null): TransactionInterface
    {
        $this->kref = $kref;
        return $this;
    }

    /**
     * Get Mref for this transaction
     */
    public function getMref(): ?string
    {
        return ($this->mref !== null) ? trim($this->mref) : null;
    }

    /**
     * Set Mref for this transaction
     */
    public function setMref(string $mref = null): TransactionInterface
    {
        $this->mref = $mref;
        return $this;
    }

    /**
     * Get Cred for this transaction
     */
    public function getCred(): ?string
    {
        return ($this->cred !== null) ? trim($this->cred) : null;
    }

    /**
     * Set Cred for this transaction
     */
    public function setCred(string $cred = null): TransactionInterface
    {
        $this->cred = $cred;
        return $this;
    }

    /**
     * Get Svwz for this transaction
     */
    public function getSvwz(): ?string
    {
        return ($this->svwz !== null) ? trim($this->svwz) : null;
    }

    /**
     * Set Svwz for this transaction
     */
    public function setSvwz(string $svwz = null): TransactionInterface
    {
        $this->svwz = $svwz;
        return $this;
    }

    // }}}
}
