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
 * Tests for Jejik\MT940\Parser\AbnAmro
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class AbnAmroTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('AbnAmro', \Jejik\MT940\Parser\AbnAmro::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/abnamro.txt'));
    }

    public function testStatement(): void
    {
        $this->assertCount(2, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('19321/1', $statement->getNumber());
        $this->assertEquals('517852257', $statement->getAccount()->getNumber());
    }

    public function testBalance(): void
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2011-05-22 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(3236.28, $balance->getAmount());
    }

    public function testTransaction(): void
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(8, $transactions);

        $this->assertEquals('2011-05-24 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('2011-05-24 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(-9.00, $transactions[0]->getAmount());

        $expected = "GIRO   428428 KPN - DIGITENNE    BETALINGSKENM.  000000042188659\r\n"
                  . "5314606715                       BETREFT FACTUUR D.D. 20-05-2011\r\n"
                  . "INCL. 1,44 BTW";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('428428', $transactions[0]->getContraAccount()->getNumber());

        $transactions = $this->statements[1]->getTransactions();
        $this->assertNotNull($transactions[1]->getContraAccount());
        $this->assertEquals('528939882', $transactions[1]->getContraAccount()->getNumber());
    }

    public function testContinuedStatement(): void
    {
        $this->assertEquals('19322/1', $this->statements[1]->getNumber());

        $balance = $this->statements[1]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals(2876.84, $balance->getAmount());

        $balance = $this->statements[1]->getClosingBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals(1849.75, $balance->getAmount());
    }

    public function testContraAccountName(): void
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertEquals('KPN - DIGITENNE', $transactions[0]->getContraAccount()->getName());

        $transactions = $this->statements[1]->getTransactions();
        $this->assertEquals('MYCOM DEN HAAG', $transactions[1]->getContraAccount()->getName());
    }
}
