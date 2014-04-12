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

namespace Jejik\Tests\MT940;

use Jejik\MT940\Reader;
use Jejik\Tests\MT940\Fixture\Balance;
use Jejik\Tests\MT940\Fixture\Statement;
use Jejik\Tests\MT940\Fixture\Transaction;

/**
 * Tests for Jejik\MT940\Reader
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultParsers()
    {
        $reader = new Reader();

        try {
            $reader->getStatements('');
        } catch (\RuntimeException $e) {
            // No parser can read an empty string
        }

        $this->assertCount(6, $reader->getParsers());
    }

    public function testAddParser()
    {
        $reader = new Reader();
        $reader->addParser('My bank', 'My\Bank');
        $this->assertEquals(array('My bank' => 'My\Bank'), $reader->getParsers());
    }

    public function testAddParserBefore()
    {
        $reader = new Reader();
        $reader->setParsers($reader->getDefaultParsers());
        $reader->addParser('My bank', 'My\Bank', 'ING');

        $parsers = array_keys($reader->getParsers());
        $index = array_search('My bank', $parsers);
        $this->assertEquals('ING', $parsers[$index + 1]);
    }

    public function testStringInjection()
    {
        $reader = new Reader();
        $reader->setParsers(array('Generic' => 'Jejik\Tests\MT940\Fixture\Parser'));

        $reader->setStatementClass('Jejik\Tests\MT940\Fixture\Statement');
        $reader->setAccountClass('Jejik\Tests\MT940\Fixture\Account');
        $reader->setContraAccountClass('Jejik\Tests\MT940\Fixture\Account');
        $reader->setTransactionClass('Jejik\Tests\MT940\Fixture\Transaction');
        $reader->setOpeningBalanceClass('Jejik\Tests\MT940\Fixture\Balance');
        $reader->setClosingBalanceClass('Jejik\Tests\MT940\Fixture\Balance');

        $statements = $reader->getStatements(file_get_contents(__DIR__ . '/Fixture/document/generic.txt'));

        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Statement', $statements[0]);
        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Account', $statements[0]->getAccount());
        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Balance', $statements[0]->getOpeningBalance());
        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Balance', $statements[0]->getClosingBalance());

        $transactions = $statements[0]->getTransactions();
        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Transaction', $transactions[0]);
    }

    public function testCallableInjection()
    {
        $reader = new Reader();
        $reader->setParsers(array('Generic' => 'Jejik\Tests\MT940\Fixture\Parser'));

        $reader->setStatementClass(function () { return new Statement(); });
        $reader->setTransactionClass(function () { return new Transaction(); });
        $reader->setOpeningBalanceClass(function () { return new Balance(); });
        $reader->setClosingBalanceClass(function () { return new Balance(); });

        $statements = $reader->getStatements(file_get_contents(__DIR__ . '/Fixture/document/generic.txt'));

        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Statement', $statements[0]);
        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Balance', $statements[0]->getOpeningBalance());
        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Balance', $statements[0]->getClosingBalance());

        $transactions = $statements[0]->getTransactions();
        $this->assertInstanceOf('Jejik\Tests\MT940\Fixture\Transaction', $transactions[0]);
    }

    public function testSkipStatement()
    {
        $reader = new Reader();
        $reader->setParsers(array('Generic' => 'Jejik\Tests\MT940\Fixture\Parser'));
        $reader->setStatementClass(function ($account, $number) {
            if ($number == '2') {
                return new Statement();
            }

            return null;
        });

        $statements = $reader->getStatements(file_get_contents(__DIR__ . '/Fixture/document/generic.txt'));
        $this->assertEquals(1, count($statements));
        $this->assertEquals('2', $statements[0]->getNumber());
    }
}
