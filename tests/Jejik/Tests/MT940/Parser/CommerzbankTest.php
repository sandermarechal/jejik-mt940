<?php

declare(strict_types=1);

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Parser\Commerzbank;
use Jejik\MT940\Reader;
use PHPUnit\Framework\TestCase;

class CommerzbankTest extends TestCase
{
    private Reader $reader;

    public function setUp(): void
    {
        $this->reader = new Reader();
        $this->reader->addParser('Commerzbank', Commerzbank::class);

        $this->context = file_get_contents(__DIR__ . '/../Fixture/document/commerzbank.txt');
    }

    public function testPassAccept(): void
    {
        $parser = $this->reader->getStatements($this->context);

        self::assertIsArray($parser);
    }
}
