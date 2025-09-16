<?php

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Balance;
use Jejik\MT940\Parser\RaiffeisenKaernten;
use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Class RaiffeisenKaerntenTest
 *
 * @package Jejik\Tests\MT940\Parser
 */
class RaiffeisenKaerntenTest extends TestCase
{
    /** @var Reader */
    private $reader;

    /** @inheritDoc */
    protected function setUp(): void
    {
        $this->reader = new Reader();
        $this->reader->addParser('RaiffeisenKaernten', RaiffeisenKaernten::class);
    }

    /**
     * @return void
     *
     * @dataProvider acceptTestDataProvider
     */
    public function testAccept(string $text, bool $expected): void
    {
        $parser = new RaiffeisenKaernten($this->reader);

        $this->assertEquals($expected, $parser->accept($text));
    }

    public function acceptTestDataProvider(): array
    {
        return [
            ['text' => file_get_contents(__DIR__ . '/../Fixture/document/raiffeisen-kaernten.txt'), 'expected' => true],
            ['text' => file_get_contents(__DIR__ . '/../Fixture/document/raiffeisen.txt'), 'expected' => false],
            ['text' => file_get_contents(__DIR__ . '/../Fixture/document/bil.txt'), 'expected' => false],
            ['text' => file_get_contents(__DIR__ . '/../Fixture/document/ing-4.txt'), 'expected' => false],
            ['text' => file_get_contents(__DIR__ . '/../Fixture/document/ing-dos.txt'), 'expected' => false],
            ['text' => file_get_contents(__DIR__ . '/../Fixture/document/sparkasse.txt'), 'expected' => false],
            ['text' => file_get_contents(__DIR__ . '/../Fixture/document/sparkasse2.txt'), 'expected' => false],
            ['text' => file_get_contents(__DIR__ . '/../Fixture/document/triodos.txt'), 'expected' => false],
        ];
    }

    public function testStatement(): void
    {
        $statements = $this->reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/raiffeisen-kaernten.txt'));

        $this->assertCount(1, $statements, 'Assert counting statements.');
        $statement = $statements[0];

        $this->assertEquals('25001/001', $statement->getNumber());
        $this->assertEquals('AT39364/12345678912', $statement->getAccount()->getNumber());
    }

    public function testBalance(): void
    {
        $statements = $this->reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/raiffeisen-kaernten.txt'));

        $balance = $statements[0]->getOpeningBalance();
        $this->assertInstanceOf(Balance::class, $balance);
        $this->assertEquals('2025-07-16 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());
    }

    public function testTransaction(): void
    {
        $statements = $this->reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/raiffeisen-kaernten.txt'));

        $transactions = $statements[0]->getTransactions();

        $this->assertCount(1, $transactions);
        $this->assertNull($transactions[0]->getContraAccount());

        $this->assertEquals(1, $transactions[0]->getAmount());
        $expectedDescription =
            "999foo\r
Test Überweisung";
        $this->assertEquals($expectedDescription, $transactions[0]->getDescription());
        $this->assertEquals('2025-07-16 00:00:00', $transactions[0]->getValueDate()->format('Y-m-d H:i:s'), 'Assert Value Date');
        $this->assertEquals('2025-07-16 00:00:00', $transactions[0]->getBookDate()->format('Y-m-d H:i:s'), 'Assert Book Date');

        $this->assertEquals('TRF', $transactions[0]->getCode());
        $this->assertEquals('NONREF', $transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());

        $this->assertEquals('999', $transactions[0]->getGVC());
        $this->assertNull($transactions[0]->getTxText());
        $this->assertNull($transactions[0]->getPrimanota());
        $this->assertNull($transactions[0]->getExtCode());

        $this->assertNull($transactions[0]->getEref());

        $this->assertNull($transactions[0]->getBIC());
        $this->assertNull($transactions[0]->getIBAN());
        $this->assertNull($transactions[0]->getAccountHolder());

        $this->assertNull($transactions[0]->getKref());
        $this->assertNull($transactions[0]->getMref());
        $this->assertNull($transactions[0]->getCred());
        $this->assertNull($transactions[0]->getSvwz());
        $this->assertNull($transactions[0]->getRawSubfieldsData());
    }
}
