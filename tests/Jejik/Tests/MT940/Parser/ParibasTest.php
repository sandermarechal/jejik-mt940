<?php

declare(strict_types=1);

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Class ParibasTest
 * @package Jejik\Tests\MT940\Parser
 */
class ParibasTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Paribas', \Jejik\MT940\Parser\Paribas::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/paribas.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(2, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('00001/00001', $statement->getNumber());
        $this->assertNotNull($statement->getAccount());
        $this->assertEquals('12345678/0123456789EUR', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2024-07-31 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());

        $closingBalance = $this->statements[0]->getClosingBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $closingBalance);
        $this->assertEquals('2024-08-01 00:00:00', $closingBalance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $closingBalance->getCurrency());
        $this->assertEquals(-10, $closingBalance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(1, $transactions);

        $this->assertNull($transactions[0]->getContraAccount());
        $this->assertEquals(-10.00, $transactions[0]->getAmount());
        $expected = "808?00ENTGELTE / FEES?20BNPP Fees - 202408DE1234567?212 - 07/2024\r\n/?32INVOICE";
        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertEquals('2024-08-01 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2024-08-01 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');
        $this->assertEquals('COM', $transactions[0]->getCode());
        $this->assertEquals('NONREF', $transactions[0]->getRef());
        $this->assertEquals('TB', $transactions[0]->getBankRef());
        $this->assertEquals('808', $transactions[0]->getGVC());
        $this->assertEquals('ENTGELTE', $transactions[0]->getTxText());
        $this->assertEquals(
            null,
            $transactions[0]->getEref()
        );
        $this->assertNull($transactions[0]->getExtCode());
        $this->assertEquals(null, $transactions[0]->getBIC());
        $this->assertEquals(null, $transactions[0]->getIBAN());
        $this->assertEquals('INVOICE', $transactions[0]->getAccountHolder());
        $this->assertEquals('BNPP Fees - 202408DE12345672 - 07/2024/', $transactions[0]->getRawSubfieldsData());
    }
}
