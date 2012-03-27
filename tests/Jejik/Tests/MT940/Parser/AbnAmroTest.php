<?php

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

/**
 * Tests for Jejik\MT940\Parser\AbnAmro
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class AbnAmroTest extends \PHPUnit_Framework_TestCase
{
    public $statements = array();

    public function setUp()
    {
        $reader = new Reader();
        $reader->addParser('AbnAmro', 'Jejik\MT940\Parser\AbnAmro');
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/abnamro.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(2, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('19321/1', $statement->getNumber());
        $this->assertEquals('517852257', $statement->getAccount());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf('Jejik\MT940\Balance', $balance);
        $this->assertEquals('2011-05-22 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(3236.28, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(8, $transactions);

        $this->assertEquals('2011-05-24 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('2011-05-24 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'));
        $this->assertEquals(-9.00, $transactions[0]->getAmount());

        $expected = "GIRO   428428 KPN - DIGITENNE    BETALINGSKENM.  000000042188659\r\n"
                  . "5314606715                       BETREFT FACTUUR D.D. 20-05-2011\r\n"
                  . "INCL. 1,44 BTW";

        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertEquals('428428', $transactions[0]->getContraAccount());

        $transactions = $this->statements[1]->getTransactions();
        $this->assertEquals('528939882', $transactions[1]->getContraAccount());
    }
}
