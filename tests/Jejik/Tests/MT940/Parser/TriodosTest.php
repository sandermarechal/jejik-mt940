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
 * Tests for Jejik\MT940\Parser\Triodos
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class TriodosTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Triodos', \Jejik\MT940\Parser\Triodos::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/triodos.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('1', $statement->getNumber());
        $this->assertNotNull($statement->getAccount());
        $this->assertEquals('390123456', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
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
                  . "ALGEMENE TUSSENREKENING KOSTEN VAN 01-10-2010 TOT EN M\r\n"
                  . "ET 31-12-2010>310390123456";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('987654321', $transactions[0]->getContraAccount()->getNumber());
    }
}
