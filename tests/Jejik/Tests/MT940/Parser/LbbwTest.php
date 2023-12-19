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
class LbbwTest extends TestCase
{
    public $statements = [];

    public $statements2 = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Lbbw', \Jejik\MT940\Parser\Lbbw::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/lbbw.txt'));
        $this->statements2 = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/lbbw2.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('1', $statement->getNumber());
        $this->assertEquals('12345678/1324357', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2021-01-20 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());

        $balance2 = $this->statements2[0]->getOpeningBalance();
        $this->assertEquals('EURO', $balance2->getCurrency());
        $this->assertEquals(0, $balance2->getAmount());

        $closingBalance2 = $this->statements2[0]->getClosingBalance();
        self::assertEquals(1, $closingBalance2->getAmount());

    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(2, $transactions);

        $this->assertNull($transactions[0]->getContraAccount());

        $this->assertEquals(-0.01, $transactions[0]->getAmount());
        $expectedDescription = "834?00KONTENPOOL?102?20BUCHUNG AUF KTO 7402050699?21BANKLEITZAHL\r
60050101?3060050101?3111111111";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2021-02-08 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2021-02-08 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertEquals('CMZ', $transactions[0]->getCode());
        $this->assertEquals('NONREF', $transactions[0]->getRef());
        $this->assertEquals(null, $transactions[0]->getBankRef());

        $this->assertEquals('834', $transactions[0]->getGVC());
        $this->assertEquals('KONTENPOOL', $transactions[0]->getTxText());
        $this->assertEquals('2', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());
        $this->assertNull($transactions[0]->getEref());
        $this->assertEquals('60050101', $transactions[0]->getBIC());
        $this->assertEquals('11111111', $transactions[0]->getIBAN());
        $this->assertNull($transactions[0]->getAccountHolder());
        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());
        $this->assertNull($transactions[0]->getSvwz());

        $this->assertEquals('NIC RICHTER', $transactions[1]->getAccountHolder());
        $this->assertEquals('SEPA-20210203175805-00154800-P1', $transactions[1]->getKref());
        $this->assertNull($transactions[1]->getMref());
        $this->assertNull($transactions[1]->getCred());
        $this->assertEquals(
            'E-MOBILITY ABRECHNUNGNR. 28 ZU VERTRAG 9433, KUNDENNUMMER 11111',
            $transactions[1]->getSvwz()
        );
    }
}
