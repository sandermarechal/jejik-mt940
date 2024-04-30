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

use Jejik\MT940\Balance;
use Jejik\MT940\Exception\NoParserFoundException;
use Jejik\MT940\Parser\Ing;
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
        /** @var Balance $balance */
        $balance = $statements[0]->getOpeningBalance();
        $this->assertInstanceOf(Balance::class, $balance);
        $this->assertEquals('2010-07-22 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0.0, $balance->getAmount());
    }

    /**
     * @dataProvider statementsProvider
     *
     * @param array $statements
     * @param int|null $numberOfTransactions
     * @param string|null $bookDate
     * @param string|null $valueDate
     * @param float|null $amount
     * @param array|null $transactionsData
     */
    public function testTransaction(
        array   $statements,
        ?int    $numberOfTransactions,
        ?string $bookDate,
        ?string $valueDate,
        ?float  $amount,
        ?array  $transactionsData
    ): void
    {
        $transactions = $statements[0]->getTransactions();
        $this->assertCount($numberOfTransactions, $transactions);

        $this->assertEquals($bookDate, $transactions[0]->getBookDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($valueDate, $transactions[0]->getValueDate());
        $this->assertEquals($amount, $transactions[0]->getAmount());

        $expected = "RC AFREKENING BETALINGSVERKEER\r\n"
            . "BETREFT REKENING 4715589 PERIODE: 01-10-2010 / 31-12-2010\r\n"
            . "ING Bank N.V. tarifering ING";

        if (!$transactionsData) {
            $this->assertEquals($expected, $transactions[0]->getDescription());
        }

        if (null !== $transactions[1]->getContraAccount()) {
            $this->assertEquals('0111111111', $transactions[1]->getContraAccount()->getNumber());
        }

        if ($transactionsData) {
            for ($i = 0; $i < count($transactions); $i++) {
                $transactionInformations = json_decode($transactionsData[$i], true);
                foreach ($transactionInformations as $key => $value) {
                    $method = 'get' . $key;
                    $this->assertEquals($value, $transactions[$i]->$method());
                }
            }
        }
    }

    /**
     * @dataProvider statementsProvider
     *
     * @param array $statements
     */
    public function testBookDate($statements)
    {
        $transactions = $statements[0]->getTransactions();
        if (null !== $transactions[6]->getValueDate()) {
            $this->assertEquals('2010-07-22 00:00:00', $transactions[6]->getValueDate()->format('Y-m-d H:i:s'));
        }
        $this->assertEquals('2010-07-23 00:00:00', $transactions[6]->getBookDate()->format('Y-m-d H:i:s'));
    }

    /**
     * @throws NoParserFoundException
     */
    public function statementsProvider(): array
    {
        $reader = new Reader();
        $reader->addParser('Ing', \Jejik\MT940\Parser\Ing::class);
        return [
            [
                $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/ing-dos.txt')),
                'number of transactions' => 7,
                'bookDate' => '2010-07-22 00:00:00',
                'valueDate' => null,
                'amount' => -25.03,
                []
            ],
            [
                $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/ing-unix-1.txt')),
                'number of transactions' => 7,
                'bookDate' => '2010-07-22 00:00:00',
                'valueDate' => null,
                'amount' => -25.03,
                []
            ],
            [
                $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/ing-unix-2.txt')),
                'number of transactions' => 7,
                'bookDate' => '2010-07-22 00:00:00',
                'valueDate' => null,
                'amount' => -25.03,
                []
            ],
            [
                $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/ing-unix-3.txt')),
                'number of transactions' => 7,
                'bookDate' => '2010-07-22 00:00:00',
                'valueDate' => null,
                'amount' => -25.03,
                []
            ],
            [
                $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/ing-4.txt')),
                'number of transactions' => 4,
                'bookDate' => '2023-04-20 00:00:00',
                'valueDate' => null,
                'amount' => -768.51,
                [
                    '{
                        "Code":"TRF", 
                        "TransactionCode":"00200", 
                        "TxText":"TOTAAL 5 VZ/", 
                        "Eref":"TOTAAL 5 VZ/", 
                        "Bic":null, 
                        "Iban":null, 
                        "AccountHolder":null, 
                        "RawSubfieldsData":"/PREF/SEPA-20230418220132-60947700-P1//REMI/USTD//TOTAAL 5 VZ/"
                    }',
                    '{
                        "Code":"TRF", 
                        "TransactionCode":"00100", 
                        "TxText":"Nr. LNT22000057 / 21.2.2023Nr. LNT22000079 / 21.3.2023Nr. LNT22000068 / 21.2.2023/", 
                        "Eref":"Nr. LNT22000057 / 21.2.2023Nr. LNT22000079 / 21.3.2023Nr. LNT22000068 / 21.2.2023/", 
                        "Bic":"ABNANL2A", 
                        "Iban":"NL71ABNA0841238***", 
                        "AccountHolder":"GREENFLUX ASSETS BV", 
                        "RawSubfieldsData":"/CNTP/NL71ABNA0841238***/ABNANL2A/GREENFLUX ASSETS BV///REMI/USTD//Nr. LNT22000057 / 21.2.2023Nr. LNT22000079 / 21.3.2023Nr. LNT22000068 / 21.2.2023/"
                    }',
                    '{
                        "Code":"TRF", 
                        "TransactionCode":"00100", 
                        "TxText":"110130590/", 
                        "Eref":"SCT-1-BO230019-R1/ 110130590/", 
                        "Bic":"RABONL2U", 
                        "Iban":"NL68RABO0106200***", 
                        "AccountHolder":"Lambrix Elektrotechniek", 
                        "RawSubfieldsData":"/EREF/SCT-1-BO230019-R1//CNTP/NL68RABO0106200***/RABONL2U/Lambrix Elektrotechniek///REMI/USTD//110130590/"
                    }',
                    '{
                        "Code":"CMI", 
                        "TransactionCode":"05001", 
                        "TxText":"POOL-M NL21INGB0650141172 POOL-S NL30INGB0008693687 PPM9455956 20/04/2023/", 
                        "Eref":"POOL-M NL21INGB0650141172 POOL-S NL30INGB0008693687 PPM9455956 20/04/2023/", 
                        "Bic":null, 
                        "Iban":"NL21INGB0650141***", 
                        "AccountHolder":"Rexel Holding Netherlands B.V.",
                        "RawSubfieldsData":"/CNTP/NL21INGB0650141***//Rexel Holding Netherlands B.V.///REMI/USTD//POOL-M NL21INGB0650141172 POOL-S NL30INGB0008693687 PPM9455956 20/04/2023/"}',
                ]
            ],
        ];
    }
}
