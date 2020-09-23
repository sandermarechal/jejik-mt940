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
 * Tests for Jejik\MT940\Parser\OldenburgischeLandesbank
 *
 * @author Dominic Richter <d.richter@powercloud.de>
 */
class OldenburgischeLandesbankTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('OldenburgischeLandesbank', \Jejik\MT940\Parser\OldenburgischeLandesbank::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/oldenburgischelandesbank.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('135/1', $statement->getNumber());
        $this->assertEquals('28020050/1324354687', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2018-07-13 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);

        $this->assertNull($transactions[0]->getContraAccount());

        $this->assertEquals(104.5, $transactions[0]->getAmount());
        $expectedDescription = "166?00GUTSCHRIFT?100004770?20WOHNBAU DIEPHOLZ GMBH?21EREF+NOTPROVIDED?22SVWZ+EWE,ENERGIEAUSWEIS RG.?23-NR. 1234567890 KD.-NR. 123?2477777?30AARBDE5W250?31DE99123456771234567888?32WOHNBAU DIEPHOLZ GMBH?35EWE VERTRIEB GMBH";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2018-07-16 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2018-07-16 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertEquals("051", $transactions[0]->getCode());
        $this->assertEquals("NONREF", $transactions[0]->getRef());
        $this->assertEquals("NONREF", $transactions[0]->getBankRef());

        $this->assertEquals('166', $transactions[0]->getGVC());
        $this->assertEquals('GUTSCHRIFT', $transactions[0]->getTxText());
        $this->assertEquals('0004770', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertEquals('NOTPROVIDED', $transactions[0]->getEref());

        $this->assertEquals('AARBDE5W250', $transactions[0]->getBIC());
        $this->assertEquals('DE99123456771234567888', $transactions[0]->getIBAN());
        $this->assertEquals('WOHNBAU DIEPHOLZ GMBH', $transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());

        $this->assertEquals('EWE,ENERGIEAUSWEIS RG.-NR. 1234567890 KD.-NR. 12377777', $transactions[0]->getSvwz());
        //$this->assertEquals('DE12345678901234567890', $transactions[0]->getContraAccount()->getNumber());
        //$this->assertEquals('Max Mustermann', $transactions[0]->getContraAccount()->getName());
    }
}
