<?php

declare(strict_types=1);

namespace Jejik\MT940\Parser;

/**
 * Class LandesBankHessen
 * @package Jejik\MT940\Parser
 */
class LandesBankHessen extends GermanBank
{

    public function getAllowedBLZ(): array
    {
        return [];
    }

    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:940311220001001'
        ];

        $mt940Identifier = substr($text, 0, 19);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        return $this->isBLZAllowed($text);
    }
}
