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
 * Tests for Jejik\MT940\Parser\DeutscheBank
 *
 * @author Dominic Richter <d.richter@powercloud.de>
 */
class DeutscheBankTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('DeutscheBank', \Jejik\MT940\Parser\DeutscheBank::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/deutschebank.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('00143/1', $statement->getNumber());
        $this->assertEquals('20070000/0123456601', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2020-06-05 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);

        $this->assertNull($transactions[0]->getContraAccount());

        $this->assertEquals(-12.35, $transactions[0]->getAmount());
        $expectedDescription = "109?00SEPA-LASTSCHR. RETOURE CORE?109075/629?20EREF+A1.200080779.\r\n400143254?21.4961336 KREF+SEPA-DA202006?2201221740-34972000-P1 MR\r
EF+2?230852HW2723821 CRED+DE41EON0?240000129793 OAMT+11,85 SVWZ+?\r
25SONSTIGE GRUENDE ENDABRECHN?26UNG NR. 500106875 ZU VERTRA?2740\r
0143254, KUNDENNUM MER 2?2802227779?30CSDBDE71XXX?31DE50712345600\r
200691329?32TESTER?33EL";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2020-06-08 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2020-06-08 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertNull($transactions[0]->getCode());
        $this->assertNull($transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());

        $this->assertEquals('109', $transactions[0]->getGVC());
        $this->assertEquals('SEPA-LASTSCHR. RETOURE CORE', $transactions[0]->getTxText());
        $this->assertEquals('9075/629', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertEquals(
            'A1.200080779.400143254.4961336 KREF+SEPA-DA20200601221740-34972000-P1 MREF+20852HW2723821 CRED+DE41EON00000129793 OAMT+11,85 SVWZ+SONSTIGE GRUENDE ENDABRECHNUNG NR. 500106875 ZU VERTRA400143254, KUNDENNUM MER 202227779',
            $transactions[0]->getEref()
        );

        $this->assertEquals('CSDBDE71XXX', $transactions[0]->getBIC());
        $this->assertEquals('DE50712345600200691329', $transactions[0]->getIBAN());
        $this->assertEquals('TESTEREL', $transactions[0]->getAccountHolder());

        $this->assertEquals(
            'SEPA-DA20200601221740-34972000-P1 MREF+20852HW2723821 CRED+DE41EON00000129793 OAMT+11,85',
            $transactions[0]->getKref()
        );
        $this->assertEquals('20852HW2723821 CRED+DE41EON00000129793 OAMT+11,85', $transactions[0]->getMref());
        $this->assertEquals('DE41EON00000129793 OAMT+11,85', $transactions[0]->getCred());

        $this->assertEquals('SONSTIGE GRUENDE ENDABRECHNUNG NR. 500106875 ZU VERTRA400143254, KUNDENNUM MER 202227779', $transactions[0]->getSvwz());
        //$this->assertEquals('DE12345678901234567890', $transactions[0]->getContraAccount()->getNumber());
        //$this->assertEquals('Max Mustermann', $transactions[0]->getContraAccount()->getName());
    }
}
