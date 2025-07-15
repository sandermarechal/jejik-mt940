<?php

declare(strict_types=1);

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Balance;
use Jejik\MT940\Exception\NoParserFoundException;
use Jejik\MT940\Parser\Solaris;
use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

/**
 * Class SolarisTest
 * @package Jejik\Tests\MT940\Parser
 *
 */
class SolarisTest extends TestCase
{
    public $statements = [];

    /**
     * @throws NoParserFoundException
     */
    public function setUp(): void
    {
        $reader = new Reader();
        $reader->addParser('Solaris', Solaris::class);
        $this->statements = $reader->getStatements(file_get_contents(__DIR__ . '/../Fixture/document/solaris.txt'));
    }

    /**
     * @return void
     */
    public function testStatement()
    {
        $this->assertCount(1, $this->statements);
        $statement = $this->statements[0];

        $this->assertEquals('0', $statement->getNumber());
        $this->assertNotNull($statement->getAccount());
        $this->assertEquals('12345678/1234567891', $statement->getAccount()->getNumber());
    }

    /**
     * @return void
     */
    public function testBalance()
    {
        $balance = $this->statements[0]->getOpeningBalance();
        $this->assertInstanceOf(Balance::class, $balance);
        $this->assertEquals('2024-11-04 00:00:00', $balance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $balance->getCurrency());
        $this->assertEquals(0, $balance->getAmount());

        $closingBalance = $this->statements[0]->getClosingBalance();
        $this->assertInstanceOf(Balance::class, $closingBalance);
        $this->assertEquals('2024-11-04 00:00:00', $closingBalance->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals('EUR', $closingBalance->getCurrency());
        $this->assertEquals(2, $closingBalance->getAmount());
    }

    /**
     * @return void
     */
    public function testTransaction()
    {
        $transactions = $this->statements[0]->getTransactions();
        $this->assertCount(1, $transactions);

        $this->assertNull($transactions[0]->getContraAccount());
        $this->assertEquals(-1, $transactions[0]->getAmount());
        $expected = '166?00SEPA-Gutschrift?30SOBKDEBBXXX?31DE11111111111111112834?32MAX MUSTERMANN';
        $this->assertEquals($expected, $transactions[0]->getDescription());
        $this->assertEquals(
            '2024-11-04 00:00:00',
            $transactions[0]->getValueDate()->format('Y-m-d H:i:s'),
            'Assert Value Date'
        );
        $this->assertEquals(
            '2024-11-04 00:00:00',
            $transactions[0]->getBookDate()->format('Y-m-d H:i:s'),
            'Assert Book Date'
        );
        $this->assertNull($transactions[0]->getCode());
        $this->assertNull($transactions[0]->getRef());
        $this->assertNull($transactions[0]->getBankRef());
        $this->assertEquals('166', $transactions[0]->getGVC());
        $this->assertEquals('SEPA-Gutschrift', $transactions[0]->getTxText());
        $this->assertEquals(
            null,
            $transactions[0]->getEref()
        );
        $this->assertNull($transactions[0]->getExtCode());
        $this->assertEquals('SOBKDEBBXXX', $transactions[0]->getBIC());
        $this->assertEquals('DE11111111111111112834', $transactions[0]->getIBAN());
        $this->assertEquals('MAX MUSTERMANN', $transactions[0]->getAccountHolder());
        $this->assertNull($transactions[0]->getRawSubfieldsData());
    }
}
