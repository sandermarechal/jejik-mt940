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
class SparkasseInterimBalanceTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Sparkasse', \Jejik\MT940\Parser\Sparkasse::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/sparkasse_interim_balance.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(3, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('00000/001', $statement->getNumber());
        $this->assertEquals('87052000/123456789', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2019-02-15 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(194.57, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[1]->getTransactions();

        $this->assertCount(1, $transactions);

        $this->assertNotNull($transactions[0]->getContraAccount());

        $this->assertEquals(-20.00, $transactions[0]->getAmount());
        $expectedDescription = "177?00ONLINE-UEBERWEISUNG?109310?20SVWZ+Apple Pay?21DATUM 19.\r\n" .
            "02.2019, 13.24 UHR?221.TAN 002153?30NTSBDEB1XXX?31DE1234567890123\r\n" .
            "4567890?32Max Mustermann?34997";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2019-02-19 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2019-02-19 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertNull($transactions[0]->getCode());
        $this->assertNull($transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());

        $this->assertEquals('177', $transactions[0]->getGVC());
        $this->assertEquals('ONLINE-UEBERWEISUNG', $transactions[0]->getTxText());
        $this->assertEquals('9310', $transactions[0]->getPrimanota());
        $this->assertEquals('997', $transactions[0]->getExtCode());

        $this->assertNull($transactions[0]->getEref());

        $this->assertEquals('NTSBDEB1XXX', $transactions[0]->getBIC());
        $this->assertEquals('DE12345678901234567890', $transactions[0]->getIBAN());
        $this->assertEquals('Max Mustermann', $transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());

        $this->assertEquals('Apple PayDATUM 19.02.2019, 13.24 UHR1.TAN 002153', $transactions[0]->getSvwz());
        $this->assertEquals('DE12345678901234567890', $transactions[0]->getContraAccount()->getNumber());
        $this->assertEquals('Max Mustermann', $transactions[0]->getContraAccount()->getName());
    }
}
