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
 * Class LandesBankHessen
 * @package Jejik\MT940\Parser
 */
class LandesBankHessen extends GermanBank
{
    public function getAllowedBLZ(): array
    {
        return ['50050000'];
    }

    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:940311220001001',
            ':20:940151220247001'
        ];

        $mt940Identifier = substr($text, 0, 19);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        return $this->isBLZAllowed($text);
    }
}
