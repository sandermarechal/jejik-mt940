<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2021 Powercloud GmbH <d.richter@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Jejik\MT940\Parser\OldenburgischeLandesbankWithDash
 *
 * @author Dominic Richter <d.richter@powercloud.de>
 */
class OldenburgischeLandesbankWithDashTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/oldenburgischelandesbankmitbindestrich.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('1/1', $statement->getNumber());
        $this->assertEquals('DE12345678900022805304', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2021-02-04 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(279998.57, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);

        $this->assertEquals('DE41292500000123456789', $transactions[0]->getContraAccount()->getNumber());

        $this->assertEquals(1727, $transactions[0]->getAmount());
        $expectedDescription = "166?00GUTSCHRIFT?100004770?20SVWZ+Kunden-Nr. 20389020 Ve?21rtrags\r\n-Nr. 1000620785?30ABCDEFGHIJK?31DE41292500000123456789?32Projektg\r\nemeinschaft XXXXXX?33+ XXXXXXGmbH + Co. KG?35EWE Vertrieb GmbH";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2021-06-17 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2021-06-17 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertEquals("051", $transactions[0]->getCode());
        $this->assertEquals("NONREF", $transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());

        $this->assertEquals('166', $transactions[0]->getGVC());
        $this->assertEquals('GUTSCHRIFT', $transactions[0]->getTxText());
        $this->assertEquals('0004770', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertNull($transactions[0]->getEref());

        $this->assertEquals('ABCDEFGHIJK', $transactions[0]->getBIC());
        $this->assertEquals('DE41292500000123456789', $transactions[0]->getIBAN());
        $this->assertEquals('Projektgemeinschaft XXXXXX+ XXXXXXGmbH + Co. KG', $transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());

        $this->assertEquals('Kunden-Nr. 20389020 Vertrags-Nr. 1000620785', $transactions[0]->getSvwz());
        $this->assertEquals('DE41292500000123456789', $transactions[0]->getContraAccount()->getNumber());
        $this->assertNull($transactions[0]->getContraAccount()->getName());
    }
}
