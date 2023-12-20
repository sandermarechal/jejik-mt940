<?php declare(strict_types=1);

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Class BayerischeLandesbankTest
 * @package Jejik\Tests\MT940\Parser
 */
class BayerischeLandesbankTest extends TestCase
{

    public $statements = [];

    /**
     * @throws \Jejik\MT940\Exception\NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('BayerischeLandesbank', \Jejik\MT940\Parser\BayerischeLandesbank::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/bayerischelandesbank.txt'));
    }

    /**
     * Test the statement
     */
    public function testStatement(): void
    {
        $this->assertCount(1, $this->statements, 'Assert counting statements.');
        $statement = $this->statements[0];

        $this->assertEquals('39/1', $statement->getNumber());
        $this->assertEquals('70050000/4213299', $statement->getAccount()->getNumber());
    }

    /**
     *  Test balance
     */
    public function testBalance(): void
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(\Jejik\MT940\Balance::class, $balance);
        $this->assertEquals('2021-02-24 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(11657017.94, $balance->getAmount());
    }

    /**
     * Test transaction with its fields
     */
    public function testTransaction(): void
    {
        $transactions = $this->statements[0]->getTransactions();

        $this->assertCount(1, $transactions);

        $this->assertNull($transactions[0]->getContraAccount());

        $this->assertEquals(0.71, $transactions[0]->getAmount());
        $expectedDescription = "192?00SEPA-LASTSCHRIFT-CORE AUSG?100000001706?20ANZ. SEPA COR 000\r\n0001?21REF. SEPA-20210217210008-40?22133200-P1     EINR.ART. DDC?\r\n23VORMERKREF. 4340-02-23-13.4?247.01.140931?25EREF+A17022021.3924\r\n92.37655?262.391933";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2021-02-25 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2021-02-25 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertNull($transactions[0]->getCode());
        $this->assertNull($transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());

        $this->assertEquals('192', $transactions[0]->getGVC());
        $this->assertEquals('SEPA-LASTSCHRIFT-CORE AUSG', $transactions[0]->getTxText());
        $this->assertEquals('0000001706', $transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());
        $this->assertEquals('A17022021.392492.376552.391933', $transactions[0]->getEref());
        $this->assertNull($transactions[0]->getBIC());
        $this->assertNull($transactions[0]->getIBAN());
        $this->assertNull($transactions[0]->getAccountHolder());
        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());
        $this->assertNull($transactions[0]->getSvwz());
        $this->assertEquals('ANZ. SEPA COR 0000001REF. SEPA-20210217210008-40133200-P1     EINR.ART. DDCVORMERKREF. 4340-02-23-13.47.01.140931EREF+A17022021.392492.376552.391933', $transactions[0]->getRawSubfieldsData());
    }

}
