<?php

declare(strict_types=1);

/*
 * This file is part of the Powercloud\MT940 (a Fork of: Jejik\MT940) library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Powercloud\Tests\MT940\Parser;

use Powercloud\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Powercloud\MT940\Parser\Knab
 *
 * @author Casper Bakker <github@casperbakker.com>
 */
class KnabTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Powercloud\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Knab', \Powercloud\MT940\Parser\Knab::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/knab.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(2, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('998/1', $statement->getNumber());
        $this->assertEquals('123456789', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Powercloud\MT940\Balance::class, $balance);
        $this->assertEquals('2014-05-07 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[1]->getTransactions();
        $this->assertCount(2, $transactions);

        $this->assertEquals('2014-07-29 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-07-29 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(-7260.0, $transactions[0]->getAmount());

        $expected = "FACTUUR 201403110, 201403113\r\n" .
                    "REK: NL65INGB0123456789/NAAM: PICQER";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('NL65INGB0123456789', $transactions[0]->getContraAccount()->getNumber());
        $this->assertEquals('PICQER', $transactions[0]->getContraAccount()->getName());

        $transactions = $this->statements[0]->getTransactions();
        $this->assertNull($transactions[0]->getContraAccount());
    }
}
