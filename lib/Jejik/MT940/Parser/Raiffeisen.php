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
 * Class Raiffeisen
 * @package Jejik\MT940\Parser
 *
 */
class Raiffeisen extends GermanBank
{
    /**
     * @inheritDoc
     */
    public function getAllowedBLZ(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:00001150-0001'
        ];

        $mt940Identifier = substr($text, 0, 17);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        return $this->isBLZAllowed($text);
    }
}
