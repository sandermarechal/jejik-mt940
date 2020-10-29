<?php

declare(strict_types=1);

/*
 * This file is part of the Powercloud\MT940 (a Fork of: Jejik\MT940) library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Powercloud\Tests\MT940;

use Powercloud\MT940\Reader;
use Powercloud\Tests\MT940\Fixture\Balance;
use Powercloud\Tests\MT940\Fixture\Statement;
use Powercloud\Tests\MT940\Fixture\Transaction;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Powercloud\MT940\Reader
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class ReaderTest extends TestCase
{
    public function testDefaultParsers()
    {
        $reader = new Reader();

        try {
            $reader->getStatements('');
            $this->fail('Expected an exception');
        } catch (\Exception $e) {
            // No parser can read an empty string
            $this->assertTrue($e->getMessage() === 'No text is found for parsing.');
        }

        $this->assertCount(15, $reader->getDefaultParsers());
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

    public function testAddParserBeforeFirst()
    {
        $reader = new Reader();
        $reader->setParsers($reader->getDefaultParsers());
        $reader->addParser('My bank', 'My\Bank', 'ABN-AMRO');

        $parsers = array_keys($reader->getParsers());

        $this->assertEquals('My bank', $parsers[0]);
        $this->assertEquals('ABN-AMRO', $parsers[1]);
    }

    /**
     * @throws \Powercloud\MT940\Exception\NoParserFoundException
     */
    public function testStringInjection()
    {
        $reader = new Reader();
        $reader->setParsers(['Generic' => \Powercloud\Tests\MT940\Fixture\Parser::class]);

        $reader->setStatementClass(\Powercloud\Tests\MT940\Fixture\Statement::class);
        $reader->setAccountClass(\Powercloud\Tests\MT940\Fixture\Account::class);
        $reader->setContraAccountClass(\Powercloud\Tests\MT940\Fixture\Account::class);
        $reader->setTransactionClass(\Powercloud\Tests\MT940\Fixture\Transaction::class);
        $reader->setOpeningBalanceClass(\Powercloud\Tests\MT940\Fixture\Balance::class);
        $reader->setClosingBalanceClass(\Powercloud\Tests\MT940\Fixture\Balance::class);

        $statements = $reader->getStatements(file_get_contents(__DIR__ . '/Fixture/document/generic.txt'));

        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Statement::class, $statements[0]);
        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Account::class, $statements[0]->getAccount());
        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Balance::class, $statements[0]->getOpeningBalance());
        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Balance::class, $statements[0]->getClosingBalance());

        $transactions = $statements[0]->getTransactions();
        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Transaction::class, $transactions[0]);
    }

    /**
     * @throws \Powercloud\MT940\Exception\NoParserFoundException
     */
    public function testCallableInjection()
    {
        $reader = new Reader();
        $reader->setParsers(array('Generic' => \Powercloud\Tests\MT940\Fixture\Parser::class));

        $reader->setStatementClass(function () {
            return new Statement();
        });
        $reader->setTransactionClass(function () {
            return new Transaction();
        });
        $reader->setOpeningBalanceClass(function () {
            return new Balance();
        });
        $reader->setClosingBalanceClass(function () {
            return new Balance();
        });

        $statements = $reader->getStatements(file_get_contents(__DIR__ . '/Fixture/document/generic.txt'));

        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Statement::class, $statements[0]);
        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Balance::class, $statements[0]->getOpeningBalance());
        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Balance::class, $statements[0]->getClosingBalance());

        $transactions = $statements[0]->getTransactions();
        $this->assertInstanceOf(\Powercloud\Tests\MT940\Fixture\Transaction::class, $transactions[0]);
    }

    /**
     * @throws \Powercloud\MT940\Exception\NoParserFoundException
     */
    public function testSkipStatement()
    {
        $reader = new Reader();
        $reader->setParsers(['Generic' => \Powercloud\Tests\MT940\Fixture\Parser::class]);
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
