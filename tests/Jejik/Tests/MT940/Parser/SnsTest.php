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

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;

/**
 * Tests for Jejik\MT940\Parser\Sns
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class SnsTest extends \PHPUnit_Framework_TestCase
{
    public $statements = array();

    public function setUp()
    {
        $reader = new Reader();
        $reader->addParser('Sns', 'Jejik\MT940\Parser\Sns');
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/sns.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(2, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('160/1', $statement->getNumber());
        $this->assertNotNull($statement->getAccount());
        $this->assertEquals('123456789', $statement->getAccount()->getNumber());
    }

    public function testOpeningBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
        $this->assertEquals('2012-06-08 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(1234.56, $balance->getAmount());
    }

    public function testClosingBalance()
    {
        $balance = $this->statements[0]->getClosingBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
        $this->assertEquals('2012-06-08 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(1209.56, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(2, $transactions);

        $this->assertEquals('2012-06-07 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('2012-06-08 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(-20.00, $transactions[0]->getAmount());

        $expected = "0987654321 marechal s\r\n"
                  . "                                                                 \r\n"
                  . "dit is een test";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('987654321', $transactions[0]->getContraAccount()->getNumber());
    }

    public function testNoTransactions()
    {
        $transactions = $this->statements[1]->getTransactions();
        $this->assertCount(0, $transactions);
    }
}
