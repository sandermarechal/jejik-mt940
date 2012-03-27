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
 * Tests for Jejik\MT940\Parser\Ing
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class IngTest extends \PHPUnit_Framework_TestCase
{
    public $statements = array();

    public function setUp()
    {
        $reader = new Reader();
        $reader->addParser('Ing', 'Jejik\MT940\Parser\Ing');
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/ing.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('000', $statement->getNumber());
        $this->assertEquals('1234567', $statement->getAccount());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
        $this->assertEquals('2010-07-22 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0.0, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(6, $transactions);

        $this->assertEquals('2010-07-22 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(null, $transactions[0]->getBookDate());
        $this->assertEquals(-25.03, $transactions[0]->getAmount());

        $expected = " RC AFREKENING BETALINGSVERKEER\r\n"
                  . "BETREFT REKENING 4715589 PERIODE: 01-10-2010 / 31-12-2010\r\n"
                  . "ING Bank N.V. tarifering ING";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertEquals('111111111', $transactions[1]->getContraAccount());
    }
}
