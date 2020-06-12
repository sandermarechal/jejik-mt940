<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2020 Powercloud GmbH <d.richter@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Jejik\MT940\Parser\Sparkasse
 *
 * @author Dominic Richter <d.richter@powercloud.de>
 */
class VolksbankenRaiffeisenbankenTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Sparkasse', \Jejik\MT940\Parser\Sparkasse::class);
        $this->statements = $reader->getStatements(
            file_get_contents(__DIR__ . '/../Fixture/document/volksbankenraiffeisenbanken.txt')
        );
    }

    public function testStatement()
    {
        $this->assertCount(8, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('0', $statement->getNumber());
        $this->assertEquals('66642399/93387', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2020-02-19 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(3085, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[1]->getTransactions();

        $this->assertCount(1, $transactions);

        $this->assertNotNull($transactions[0]->getContraAccount());

        $this->assertEquals(80, $transactions[0]->getAmount());
        $expectedDescription = "166?00UEBERWEISUNG?10931?20SVWZ+Musical Emilio CD\r
?21IBAN: DE835?2200105179219844142 BIC: SBCR\r
?23DE66 ?30SBCRDE66?31DE83500105179219844142?32Antonio Sueto";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2020-02-21 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertNull($transactions[0]->getBookDate());

        $this->assertEquals('MSC', $transactions[0]->getCode());
        $this->assertEquals('', $transactions[0]->getRef());
        $this->assertEquals('', $transactions[0]->getBankRef());

        $this->assertEquals('166', $transactions[0]->getGVC());
        $this->assertEquals('UEBERWEISUNG', $transactions[0]->getTxText());
        $this->assertEquals('931', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertNull($transactions[0]->getEref());

        $this->assertEquals('SBCRDE66', $transactions[0]->getBIC());
        $this->assertEquals('DE83500105179219844142', $transactions[0]->getIBAN());
        $this->assertEquals('Antonio Sueto', $transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());

        $this->assertEquals('Musical Emilio CDIBAN: DE83500105179219844142 BIC: SBCRDE66', $transactions[0]->getSvwz());
        $this->assertEquals('DE83500105179219844142', $transactions[0]->getContraAccount()->getNumber());
        $this->assertNull($transactions[0]->getContraAccount()->getName());
    }
}
