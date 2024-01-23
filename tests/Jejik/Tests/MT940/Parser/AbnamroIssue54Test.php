<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2024 Dominic Richter <d.richter@chargecloud.de>
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
 * @author Sander Marechal <d.richter@chargecloud.de>
 */
class AbnamroIssue54Test extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('AbnAmro', \Jejik\MT940\Parser\AbnAmro::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/abnamro2.txt'));
    }

    public function testStatement(): void
    {
        $this->assertCount(1, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('13501/1', $statement->getNumber());
        $this->assertEquals('123456789', $statement->getAccount()->getNumber());
    }

    public function testBalance(): void
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2012-05-11 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(5138.61, $balance->getAmount());
    }

    public function testTransaction(): void
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(1, $transactions);

        $this->assertEquals('2012-05-12 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('2012-05-14 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(500.01, $transactions[0]->getAmount());

        $expected = "/TRTP/SEPA OVERBOEKING/IBAN/FR12345678901234/BIC/GEFRADAM\r\n"
                    . "/NAME/QASD JGRED/REMI/Dit zijn de omschrijvingsregels/EREF/NOTPRO\r\n"
                    . "VIDED";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('428428', $transactions[0]->getContraAccount()->getNumber());

        $transactions = $this->statements[1]->getTransactions();
        $this->assertNotNull($transactions[1]->getContraAccount());
        $this->assertEquals('528939882', $transactions[1]->getContraAccount()->getNumber());
    }

    public function testContinuedStatement(): void
    {
        $this->assertEquals('13501/1', $this->statements[0]->getNumber());

        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals(5138.61, $balance->getAmount());

        $balance = $this->statements[0]->getClosingBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals(5638.62, $balance->getAmount());
    }

    public function testContraAccountName(): void
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertEquals('XXXXXXXXX', $transactions[0]->getContraAccount()->getName());

        $transactions = $this->statements[1]->getTransactions();
        $this->assertEquals('YYYYYYYYY', $transactions[1]->getContraAccount()->getName());
    }
}
