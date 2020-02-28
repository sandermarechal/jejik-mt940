<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\Tests\MT940\Fixture;

use Jejik\MT940\Parser\AbstractParser;

/**
 * Parser for the generic fixture document
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Parser extends AbstractParser
{
    /**
     * Test if the document is our generic document
     *
     * @param string $text
     * @return bool
     */
    public function accept($text): bool
    {
        return substr($text, 0, 11) === ':20:GENERIC';
    }
}
