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
 * Tests for Jejik\MT940\Parser\Rabobank
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class RabobankTest extends \PHPUnit_Framework_TestCase
{
    public $statements = array();

    public function setUp()
    {
        $reader = new Reader();
        $reader->addParser('Rabobank', 'Jejik\MT940\Parser\Rabobank');
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/rabobank.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(3, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('00000/00', $statement->getNumber());
        $this->assertEquals('129199348', $statement->getAccount());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
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
        $this->assertEquals('733959555', $transactions[0]->getContraAccount());
    }
}
