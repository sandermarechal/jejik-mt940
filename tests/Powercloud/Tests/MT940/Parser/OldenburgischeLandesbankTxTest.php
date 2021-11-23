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

namespace Powercloud\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Jejik\MT940\Parser\OldenburgischeLandesbank
 *
 * @author Dominic Richter <d.richter@powercloud.de>
 */
class OldenburgischeLandesbankTxTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('OldenburgischeLandesbank', \Jejik\MT940\Parser\OldenburgischeLandesbank::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/K4262927_20200905-080000-952.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('172/1', $statement->getNumber());
        $this->assertEquals('28020050/1426292700', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2020-09-04 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);

        $this->assertNull($transactions[0]->getContraAccount());

        $this->assertEquals(230, $transactions[0]->getAmount());
        $expectedDescription = "166?00GUTSCHRIFT?100004772?20EREF+SCP 100 / 0082002528?21SVWZ+D 8\r
03020001000145464 +?221000145463 XXXXXXX,XXXXXX?30OLBODEH2XXX?31D\r
E59280200501000000000?32EWE VERTRIEB GmbH?35EWE VERTRIEB powerclo\r
ud";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2020-09-04 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2020-09-04 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertEquals("051", $transactions[0]->getCode());
        $this->assertEquals("NONREF", $transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());

        $this->assertEquals('166', $transactions[0]->getGVC());
        $this->assertEquals('GUTSCHRIFT', $transactions[0]->getTxText());
        $this->assertEquals('0004772', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertEquals('SCP 100 / 0082002528', $transactions[0]->getEref());

        $this->assertEquals('OLBODEH2XXX', $transactions[0]->getBIC());
        $this->assertEquals('DE59280200501000000000', $transactions[0]->getIBAN());
        $this->assertEquals('EWE VERTRIEB GmbH', $transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());
        $this->assertEquals('D 803020001000145464 +1000145463 XXXXXXX,XXXXXX', $transactions[0]->getSvwz());
        //$this->assertEquals('DE12345678901234567890', $transactions[0]->getContraAccount()->getNumber());
        //$this->assertEquals('Max Mustermann', $transactions[0]->getContraAccount()->getName());
    }
}
