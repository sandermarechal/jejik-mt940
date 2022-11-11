<?php

declare(strict_types=1);

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Parser\Raiffeisen;
use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

class RaiffeisenTest extends TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Raiffeisen', Raiffeisen::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/raiffeisen.txt'));
    }

    public function testStatement(): void
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('001/0001', $statement->getNumber());
        $this->assertEquals('CCRALULL/LU1234567890123456789', $statement->getAccount()->getNumber());
    }

    public function testBalance(): void
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2022-02-28 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction(): void
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);
        $this->assertNull($transactions[0]->getContraAccount());

        $this->assertEquals(1, $transactions[0]->getAmount());
        $expectedDescription =
            "229\r
?00BONIFICATION\r
?20BONIFICATION\r
?21test trt\r
?22FT22059PGV08\r
?30CCRALULLXXX\r
?32ABC\r
?60ABC\r
?612 Max Mustermann,\r
?62L-3372 LEUDELANGE";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2022-02-28 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2022-02-28 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertNull($transactions[0]->getCode());
        $this->assertNull($transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());

        $this->assertEquals('229', $transactions[0]->getGVC());
        $this->assertEquals('BONIFICATION', $transactions[0]->getTxText());
        $this->assertNull($transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertNull($transactions[0]->getEref());

        $this->assertEquals('CCRALULLXXX', $transactions[0]->getBIC());
        $this->assertNull($transactions[0]->getIBAN());
        $this->assertEquals("ABCABC2 Max Mustermann,L-3372 LEUDELANGE", $transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());
        $this->assertNull($transactions[0]->getSvwz());
        $this->assertEquals('BONIFICATIONtest trtFT22059PGV08', $transactions[0]->getRawSubfieldsData());
    }
}
