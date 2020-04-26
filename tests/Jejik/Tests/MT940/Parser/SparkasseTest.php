<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2020 Powercloud GmbH <d.richter@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Jejik\MT940\Parser\Sparkasse
 *
 * @author Dominic Richter <d.richter@powercloud.de>
 */
class SparkasseTest extends TestCase
{
    public $statements = [];

    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Sparkasse', \Jejik\MT940\Parser\Sparkasse::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/sparkasse.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(2, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('00000/001', $statement->getNumber());
        $this->assertEquals('87052000/123456789', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2019-02-15 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(194.57, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[1]->getTransactions();
        $this->assertCount(1, $transactions);

        $this->assertEquals('2019-02-19 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2019-02-19 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');
        $this->assertEquals(-20.00, $transactions[0]->getAmount());

        $expected = "177?00ONLINE-UEBERWEISUNG?109310?20SVWZ+Apple Pay?21DATUM 19.\r\n" .
            "02.2019, 13.24 UHR?221.TAN 002153?30NTSBDEB1XXX?31DE1234567890123\r\n" .
            "4567890?32Max Mustermann?34997";


        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[0]->getContraAccount());
        $this->assertEquals('DE12345678901234567890', $transactions[0]->getContraAccount()->getNumber());
        $this->assertEquals('Max Mustermann', $transactions[0]->getContraAccount()->getName());
    }
}
