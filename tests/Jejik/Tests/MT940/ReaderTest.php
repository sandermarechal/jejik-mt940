<?php

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\Tests\MT940;

use Jejik\MT940\Reader;

/**
 * Tests for Jejik\MT940\Reader
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultParsers()
    {
        $reader = new Reader();

        try {
            $reader->getStatements('');
        } catch (\RuntimeException $e) {
            // No parser can read an empty string
        }

        $this->assertCount(4, $reader->getParsers());
    }

    public function testAddParser()
    {
        $reader = new Reader();
        $reader->addParser('My bank', 'My\Bank');
        $this->assertEquals(array('My bank' => 'My\Bank'), $reader->getParsers());
    }

    public function testAddParserBefore()
    {
        $reader = new Reader();
        $reader->setParsers($reader->getDefaultParsers());
        $reader->addParser('My bank', 'My\Bank', 'ING');

        $parsers = array_keys($reader->getParsers());
        $index = array_search('My bank', $parsers);
        $this->assertEquals('ING', $parsers[$index + 1]);
    }
}
