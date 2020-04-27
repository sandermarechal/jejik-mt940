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

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Jejik\MT940\Parser\Rabobank
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class RabobankTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Rabobank', \Jejik\MT940\Parser\Rabobank::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/rabobank.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(4, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('110614', $statement->getNumber());
        $this->assertNotNull($statement->getAccount());
        $this->assertEquals('129199348', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2011-06-14 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(473.17, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[2]->getTransactions();
        $this->assertCount(2, $transactions);

        $this->assertEquals('2011-06-17 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(null, $transactions[0]->getBookDate());
        $this->assertEquals(-44.95, $transactions[0]->getAmount());

        $expected = "BETALINGSKENM.  123456789\r\n"
                  . "FACTUURNUMMER 987654321";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('733959555', $transactions[0]->getContraAccount()->getNumber());
        $this->assertEquals('T-MOBILE NETHERLANDS BV', $transactions[0]->getContraAccount()->getName());
    }

    public function testNonrefContraAccountName()
    {
        $transactions = $this->statements[2]->getTransactions();

        $this->assertNotNull($transactions[1]->getContraAccount());
        $this->assertNull($transactions[1]->getContraAccount()->getNumber());
        $this->assertEquals('TOMTE TUMMETOT AMERSFOORT', $transactions[1]->getContraAccount()->getName());
    }

    // Should also match when the transaction type is NMSC
    public function testNMSC()
    {
        $transactions = $this->statements[3]->getTransactions();

        $this->assertEquals('2012-08-29 00:00:00', $transactions[1]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(-6.20, $transactions[1]->getAmount());
        $this->assertNotNull($transactions[1]->getContraAccount());
        $this->assertEquals('29225', $transactions[1]->getContraAccount()->getNumber());
    }
}
