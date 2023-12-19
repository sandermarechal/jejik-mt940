<?php

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;

/**
 * Class LandesBankHessenTest
 * @package Jejik\Tests\MT940\Parser
 */
class LandesBankHessenTest extends \PHPUnit\Framework\TestCase
{
    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('LandesBankHessen', \Jejik\MT940\Parser\LandesBankHessen::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/landesbankhessen.txt'));
    }

    public function testStatement()
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('1', $statement->getNumber());
        $this->assertEquals('50050000/0090085309', $statement->getAccount()->getNumber());
    }

    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2020-12-31 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);
        $this->assertNull($transactions[0]->getContraAccount());

        $this->assertEquals(0, $transactions[0]->getAmount());
        $expectedDescription = "835?00SONSTIGE NICHT DEF.GV-ARTEN?1059777?20 RECHNUNGSABSCHLUSS P\r\nER?2131.12.2020 GEM. 355 HGB?22 ABSCHL.SALDO PER: 31.12.20?23EUR\r\n0,00?24 DIESER KONTOAUSZUG GILT IM?25ZUSAMMENHANG MIT DEN?26ZUGRU\r\nNDELIEGENDEN?27VERTRAEGEN ALS RECHNUNG?28 IM SINNE USTG.";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2020-12-31 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2020-12-31 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertEquals('MSC' , $transactions[0]->getCode());
        $this->assertEquals('NONREF', $transactions[0]->getRef());
        $this->assertEquals(null, $transactions[0]->getBankRef());

        $this->assertEquals('835', $transactions[0]->getGVC());
        $this->assertEquals('SONSTIGE NICHT DEF.GV-ARTEN', $transactions[0]->getTxText());
        $this->assertEquals('59777', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertNull($transactions[0]->getEref());

        $this->assertNull($transactions[0]->getBIC());
        $this->assertNull($transactions[0]->getIBAN());
        $this->assertNull($transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());

        $this->assertNull($transactions[0]->getSvwz());
        $this->assertNull($transactions[0]->getContraAccount());
    }
}
