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
 * Tests for Bug CORE-5401
 *
 * @author Dominic Richter <d.richter@powercloud.de>
 */
class BugCore5401Test extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/bug-core-5401.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('00111/1', $statement->getNumber());
        $this->assertEquals('DE19662800530622160900', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        // Informations from Field :60x:
        $this->assertEquals('2021-03-30 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);
        // Valuta
        $this->assertEquals('2021-03-31 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        // Buchungsdatum
        $this->assertEquals('2021-03-31 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');
        // Betrag
        $this->assertEquals(50, $transactions[0]->getAmount());

        // EReferenz -> NONREF
        $this->assertEquals('NONREF', $transactions[0]->getRef());

        // Bankreferenz -> 2109025460313532
        $this->assertEquals('2109025460313532', $transactions[0]->getBankRef());

        $this->assertNull($transactions[0]->getContraAccount());

        $expectedDescription = "166?00SEPA-GUTSCHRIFT?109075/611?20EREF+NOTPROVIDED SVWZ+VERTR?21\r
:KO:NR:401113172APRIL 2021?30DEUTDEDB237?31DE93230707000621787100\r
?32XXXXXXA MXXXXXX";
//        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());

        $this->assertNull($transactions[0]->getCode());

        $this->assertEquals('166', $transactions[0]->getGVC());
        $this->assertEquals('SEPA-GUTSCHRIFT', $transactions[0]->getTxText());
        $this->assertEquals('9075/611', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        // $this->assertEquals('NOTPROVIDED', $transactions[0]->getEref());

//        $this->assertEquals('DEUTDEDB237', $transactions[0]->getBIC());
//        $this->assertEquals('DE93230707000621787100', $transactions[0]->getIBAN());
//        $this->assertEquals('XXXXXXA MXXXXXX', $transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());

//        $this->assertEquals('VERTR:KO:NR:401113172APRIL 2021', $transactions[0]->getSvwz());
        //$this->assertEquals('DE12345678901234567890', $transactions[0]->getContraAccount()->getNumber());
        //$this->assertEquals('Max Mustermann', $transactions[0]->getContraAccount()->getName());
    }
}
