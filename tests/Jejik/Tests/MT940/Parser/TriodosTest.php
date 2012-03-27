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
 * Tests for Jejik\MT940\Parser\Triodos
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class TriodosTest extends \PHPUnit_Framework_TestCase
{
    public $statements = array();

    public function setUp()
    {
        $reader = new Reader();
        $reader->addParser('Triodos', 'Jejik\MT940\Parser\Triodos');
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/triodos.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('1', $statement->getNumber());
        $this->assertEquals('390123456', $statement->getAccount());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
        $this->assertEquals('2011-01-01 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(4975.09, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(2, $transactions);

        $this->assertEquals('2011-01-01 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(null, $transactions[0]->getBookDate());
        $this->assertEquals(-15.70, $transactions[0]->getAmount());

        $expected = "000>100987654321\r\n"
                  . ">20ALGEMENE TUSSENREKENING KOS>21TEN VAN 01-10-2010 TOT EN M\r\n"
                  . ">22ET 31-12-2010>310390123456";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertEquals('987654321', $transactions[0]->getContraAccount());
    }
}
