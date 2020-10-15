<?php

declare(strict_types=1);

/*
 * This file is part of the Powercloud\MT940 (a Fork of: Jejik\MT940) library
 *
 * Copyright (c) 2020 Powercloud GmbH <d.richter@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Powercloud\MT940\Parser;

use Powercloud\MT940\Exception\UserException;

class SpecificGermanBankParser extends \Powercloud\MT940\Parser\GermanBank
{
    /** @var string  */
    private $knownTransactionReferenceNumber;

    /** @var null */
    private $exception = null;

    /**
     * Creates an instance of this parser. It will only accept statements with the given transaction reference number.
     *
     * @param \Powercloud\MT940\Reader $reader
     * @param string $knownTransactionReferenceNumber The given number that will be accepted by this parser.
     */
    public function __construct(
        \Powercloud\MT940\Reader $reader,
        string $knownTransactionReferenceNumber
    ) {
        parent::__construct($reader);
        $this->knownTransactionReferenceNumber = $knownTransactionReferenceNumber;
    }

    /**
     * Method exists to fit the interface definition, but should not be called and throws always an exception.
     *
     * @return array
     */
    public function getAllowedBLZ(): array
    {
        throw new \RuntimeException(
            'Bankaccount statements are not checked by allowed BLZ if an explicit transaction reference number is given.'
        );
    }

    /**
     * Test if the document can be read by the parser. In this case this means, that the transaction reference number in
     * the bankaccount statement contains the expected reference number. (It does not need to be equal)
     *
     * @param string $text The MT940 document
     * @return bool True if the transaction reference number is correct, false if not.
     */
    public function accept(string $text): bool
    {
        // set all linebreaks to \r\n
        $this->checkCRLF($text);

        $documentTransactionReferenceNumber = $this->getTransactionReferenceNumber($text);
        return strpos($documentTransactionReferenceNumber, $this->knownTransactionReferenceNumber) !== false;
    }

    /**
     * Get the exception that has been saved, when the accept() method returned false last time.
     *
     * @return UserException|null The error message if one has been saved or null if not.
     */
    public function getException(): ?UserException
    {
        return $this->exception;
    }
}
