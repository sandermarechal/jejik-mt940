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
 * Tests for Jejik\MT940\Parser\Ing
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class IngTest extends TestCase
{

    /**
     * @dataProvider statementsProvider
     *
     * @param array $statements
     */
    public function testStatement($statements)
    {
        $this->assertCount(1, $statements);
        $statement = $statements[0];

        $this->assertEquals('000', $statement->getNumber());
        $this->assertNotNull($statement->getAccount());
        $this->assertEquals('1234567', $statement->getAccount()->getNumber());
    }

    /**
     * @dataProvider statementsProvider
     *
     * @param array $statements
     */
    public function testBalance($statements)
    {
        /** @var \Jejik\MT940\Balance $balance */
        $balance = $statements[0]->getOpeningBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
        $this->assertEquals('2010-07-22 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0.0, $balance->getAmount());
    }

    /**
     * @dataProvider statementsProvider
     *
     * @param array $statements
     */
    public function testTransaction($statements)
    {
        $transactions = $statements[0]->getTransactions();
        $this->assertCount(7, $transactions);

        $this->assertEquals('2010-07-22 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(null, $transactions[0]->getValueDate());
        $this->assertEquals(-25.03, $transactions[0]->getAmount());

        $expected = " RC AFREKENING BETALINGSVERKEER\r\n"
                  . "BETREFT REKENING 4715589 PERIODE: 01-10-2010 / 31-12-2010\r\n"
                  . "ING Bank N.V. tarifering ING";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertNotNull($transactions[1]->getContraAccount());
        $this->assertEquals('0111111111', $transactions[1]->getContraAccount()->getNumber());
    }

    /**
     * @dataProvider statementsProvider
     *
     * @param array $statements
     */
    public function testBookDate($statements)
    {
        $transactions = $statements[0]->getTransactions();
        $this->assertEquals('2010-07-22 00:00:00', $transactions[6]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('2010-07-23 00:00:00', $transactions[6]->getBookDate()->format('Y-m-d H:i:s'));
    }

    /**
     * @dataProvider statementsProvider
     */
    public function statementsProvider()
    {
        $reader = new Reader();
        $reader->addParser('Ing', 'Jejik\MT940\Parser\Ing');
        
        return array(
            array($reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/ing-dos.txt'))),
            array($reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/ing-unix.txt'))),
        );
    }
}
