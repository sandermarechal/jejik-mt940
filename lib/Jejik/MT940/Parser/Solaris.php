<?php

declare(strict_types=1);

namespace Jejik\MT940\Parser;

/**
 * Class Solaris
 * @package Jejik\MT940\Parser
 *
 */
class Solaris extends GermanBank
{

    /**
     * @inheritDoc
     */
    public function getAllowedBLZ(): array
    {
        return ['11010100'];
    }

    /**
     * @inheritDoc
     */
    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:241104ef55e6f763'
        ];

        $mt940Identifier = substr($text, 0, 20);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        return $this->isBLZAllowed($text);
    }
}
