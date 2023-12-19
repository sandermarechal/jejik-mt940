<?php


declare(strict_types=1);

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

class BilTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Bil', \Jejik\MT940\Parser\Bil::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/bil.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('00001/001', $statement->getNumber());
        $this->assertNotNull($statement->getAccount());
        $this->assertEquals('BILLLULLXXX/LU540026121160200600', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2021-12-06 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());

        $closingBalance = $this->statements[0]->getClosingBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $closingBalance);
        $this->assertEquals('2022-01-18 00:00:00', $closingBalance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $closingBalance->getCurrency());
        $this->assertEquals(-4, $closingBalance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(1, $transactions);

        $this->assertNull($transactions[0]->getContraAccount());
        $this->assertEquals(-4.00, $transactions[0]->getAmount());
        $expected = "808?00FORFAIT MENSUEL ENVOI DE COURRIER?20COMPTE . IBAN LU54 0026 1211 6020 0?21600";
        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertEquals('2022-01-17 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2022-01-17 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');
        $this->assertEquals('COM', $transactions[0]->getCode());
        $this->assertEquals('NONREF', $transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());
        $this->assertEquals('808', $transactions[0]->getGVC());
        $this->assertEquals('FORFAIT MENSUEL ENVOI DE COURRIER', $transactions[0]->getTxText());
        $this->assertEquals(
            null,
            $transactions[0]->getEref()
        );
        $this->assertNull($transactions[0]->getExtCode());
        $this->assertEquals(null, $transactions[0]->getBIC());
        $this->assertEquals(null, $transactions[0]->getIBAN());
        $this->assertEquals(null, $transactions[0]->getAccountHolder());
        $this->assertEquals('COMPTE . IBAN LU54 0026 1211 6020 0600', $transactions[0]->getRawSubfieldsData());
    }
}
