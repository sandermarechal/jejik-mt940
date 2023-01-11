<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library and tests the RDR Field in sepa
 * file.
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
class SparkasseRcrTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Sparkasse', \Jejik\MT940\Parser\Sparkasse::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/sparkasse3.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('00000/001', $statement->getNumber());
        $this->assertEquals('DE11222220220222220200', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2020-02-19 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(931052.29, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);
        $this->assertNotNull($transactions[0]->getContraAccount());

        $this->assertEquals(-1027.25, $transactions[0]->getAmount());
        $expectedDescription = "899?00STORNO?109392?20STORNO RECHNUNGSABSCHLUSS?21PER 01.02.2\r\n020?3050652124?31900932005?32GEBÜHREN MANUELL GG UST-FRE?33I";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2020-02-01 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2020-02-19 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertNull($transactions[0]->getCode());
        $this->assertNull($transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());

        $this->assertEquals('899', $transactions[0]->getGVC());
        $this->assertEquals('STORNO', $transactions[0]->getTxText());
        $this->assertEquals('9392', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertNull($transactions[0]->getEref());

        $this->assertEquals('50652124', $transactions[0]->getBIC());
        $this->assertEquals('900932005', $transactions[0]->getIBAN());
        $this->assertEquals('GEBÜHREN MANUELL GG UST-FREI', $transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());

        $this->assertNull($transactions[0]->getSvwz());
        $this->assertEquals('900932005', $transactions[0]->getContraAccount()->getNumber());
        $this->assertNull($transactions[0]->getContraAccount()->getName());
    }
}
