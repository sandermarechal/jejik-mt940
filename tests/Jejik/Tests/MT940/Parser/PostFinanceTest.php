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
use PHPUnit\Framework\TestCase;

/**
 * Tests for Jejik\MT940\Parser\PostFinance
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class PostFinanceTest extends TestCase
{
    public $statements = array();

    public function setUp()
    {
        $reader = new Reader();
        $reader->addParser('PostFinance', 'Jejik\MT940\Parser\PostFinance');
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/postfinance.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(2, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('999/1', $statement->getNumber());
        $this->assertEquals('123456789', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
        $this->assertEquals('2013-11-30 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('CHF', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(2, $transactions);

        $this->assertEquals('2013-12-09 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('2013-12-09 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(79.7, $transactions[0]->getAmount());

        $expected = "GIRO AUS ONLINE-SIC 80532 AUFTRAGGEBER: JANE DOE EXAMPLESTRASSE\r\n"
                  . " 10 1234 XXXX 131209CH98765432 MITTEILUNGEN: RECHNUNG XXXXXXXXXXXX";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('98765432', $transactions[0]->getContraAccount()->getNumber());

        $transactions = $this->statements[1]->getTransactions();
        $this->assertNull($transactions[1]->getContraAccount());
    }
}
