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
 * Tests for Jejik\MT940\Parser\Rabobank with new IBAN format
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class RabobankIbanTest extends TestCase
{
    public $statements = array();

    public function setUp()
    {
        $reader = new Reader();
        $reader->addParser('Rabobank', 'Jejik\MT940\Parser\Rabobank');
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/rabobank-iban.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(2, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('130101', $statement->getNumber());
        $this->assertNotNull($statement->getAccount());
        $this->assertEquals('NL71RABO0123456789', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
        $this->assertEquals('2013-01-01 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(1000, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(2, $transactions);

        $this->assertEquals('2013-01-01 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(null, $transactions[0]->getBookDate());
        $this->assertEquals(-25, $transactions[0]->getAmount());

        $expected = "/EREF/01-01-2013 12:00 0030000987654321/BENM//NAME/CONTRA ACCOUN\r\n"
                  . "T HOLDER/REMI//ISDT/2013-07-11";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('NL70ABNA0987654321', $transactions[0]->getContraAccount()->getNumber());
        $this->assertEquals('CONTRA ACCOUNT HOLDER', $transactions[0]->getContraAccount()->getName());
    }
}
