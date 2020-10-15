<?php

declare(strict_types=1);

/*
 * This file is part of the Powercloud\MT940 (a Fork of: Jejik\MT940) library
 *
 * Copyright (c) 2020 Powercloud GmbH <l.fürderer@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Powercloud\Tests\MT940\Parser;

/**
 * Class GeneralGermanBankTest A positive and negative test for SpecificGermanBankParser
 * @package Jejik\Tests\MT940\Parser
 */
class SpecificGermanBankTest extends \PHPUnit\Framework\TestCase
{
    private $statements;

    /**
     * Tries to parse an example MT940 file using the SpecificGermanBankParser
     *
     * @param string $expectedTransactionReferenceNumber The number to give to the parser
     * @return array The resulting statements
     * @throws \Exception If the parser failed to parse the example file
     */
    private function parseExampleFile(string $expectedTransactionReferenceNumber): array
    {
        $reader = new \Powercloud\MT940\Reader();
        $reader->addParser(
            'specific parser',
            \Powercloud\MT940\Parser\SpecificGermanBankParser::class,
            null,
            [$expectedTransactionReferenceNumber]
        );
        return $reader->getStatements(<<<EOF
:20:EXAMPLECODE
:25:DE19662800530622160900
:28C:00002/1
:60F:C000000EUR0,00
:61:1911071107C396,00N025NONREF
:86:191?00UEBERWSG?100004772?20KREF+SEPA-20191107140301-59190101-P1?30COBADEFFXXX?31DE34200400612345533292?32SEPA?35Abnahme 3240
:62F:C190417EUR0
EOF
        );
    }

    /**
     * Test the SpecificGermanBankParser with a correct transaction reference number.
     *
     * @throws \Exception
     */
    public function testMatchingStatement(): void
    {
        // 'MPLECO' is contained in 'EXAMPLECODE', so this should be successful.
        $statements = $this->parseExampleFile('MPLECO');
        $this->assertCount(1, $statements);
    }

    /**
     * Test the SpecificGermanBankParser with an incorrect transaction reference number.
     *
     * @throws \Exception
     */
    public function testNonMatchingStatement(): void
    {
        // 'OTHER' is not contained in 'EXAMPLECODE', so this should be unsuccessful.
        try {
            $statements = $this->parseExampleFile('OTHER');
            $this->fail('expected an exception');
        } catch (\Powercloud\MT940\Exception\NoParserFoundException $e) {
            $this->assertEquals('No suitable parser found.', $e->getMessage());
        }
    }
}
