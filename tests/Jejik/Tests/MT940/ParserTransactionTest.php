<?php

declare(strict_types=1);

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Reader;
use Jejik\MT940\TransactionInterface;
use Jejik\Tests\MT940\Fixture\Parser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class ParserTransactionTest
 *
 * @package Jejik\Tests\MT940\Parser
 */
class ParserTransactionTest extends TestCase
{
    /**
     * @dataProvider createDataToTestParsingTheTransactions
     *
     * @param array $lines
     * @param string|null $exceptionName
     * @param array $asserts
     * @return void
     */
    public function testParsingTheTransactions(array $lines, ?string $exceptionName, array $asserts): void
    {
        $readerClass = new Reader();
        $parser = new class($readerClass) extends Parser {
            public function getTransactions($lines): TransactionInterface
            {
                return $this->transaction($lines);
            }

        };

        if (null !== $exceptionName) {
            $this->expectException($exceptionName);
        }

        $transaction = $parser->getTransactions($lines);

        if (null === $exceptionName) {
            $this->assertInstanceOf(TransactionInterface::class, $transaction);
            $this->assertSame($asserts['amount'], $transaction->getAmount());
            $this->assertSame($asserts['description'], $transaction->getDescription());
        }
    }

    /**
     * @return array[]
     */
    public function createDataToTestParsingTheTransactions(): array
    {
        return [
            'Succeeds on creditor' => [
                'lines' => ['100722C12,34NOV NONREF', 'Test'],
                'exception' => null,
                'asserts' => [
                    'amount' => 12.34,
                    'description' => 'Test'
                ]
            ],
            'Succeeds on storno-creditor' => [
                'lines' => ['100722RC12,34NOV NONREF', 'Test'],
                'exception' => null,
                'asserts' => [
                    'amount' => 12.34,
                    'description' => 'Test'
                ]
            ],
            'Succeeds on debitor' => [
                'lines' => ['100722D12,34NOV NONREF', 'Test'],
                'exception' => null,
                'asserts' => [
                    'amount' => -12.34,
                    'description' => 'Test'
                ]
            ],
            'Succeeds on storno-debitor' => [
                'lines' => ['100722RD12,34NOV NONREF', 'Test'],
                'exception' => null,
                'asserts' => [
                    'amount' => 12.34,
                    'description' => 'Test'
                ]
            ],
            'Fails, because debitor/creditor ID x is not allowed' => [
                'lines' => ['100722X12,34NOV NONREF', ' Test Bank Identification'],
                'exception' => RuntimeException::class,
                'asserts' => []
            ]
        ];
    }
}
