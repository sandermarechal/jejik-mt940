<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2023 Sennur Tas - chargecloud GmbH
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\MT940\Parser;

/**
 * Class BayerischeLandesbank
 * @package Jejik\MT940\Parser
 */
class BayerischeLandesbank extends GermanBank
{
    /**
     * @return string[]
     */
    public function getAllowedBLZ(): array
    {
        return ['70050000'];
    }

    /**
     * @param string $text
     * @return bool
     */
    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:21766916',
        ];

        $mt940Identifier = substr($text, 0, 12);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        return $this->isBLZAllowed($text);
    }
}
