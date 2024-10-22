<?php

declare(strict_types=1);

namespace Jejik\MT940\Parser;

/**
 * Class Paribas
 * @package Jejik\MT940\Parser
 */
class Paribas extends GermanBank
{
    /**
     * @inheritDoc
     */
    public function getAllowedBLZ(): array
    {
        return [51210600];
    }

    /**
     * @inheritDoc
     */
    public function accept(string $text): bool
    {
        $allowedUniqueIdentifiers = [
            ':20:TELEREPORTING'
        ];

        $mt940Identifier = substr($text, 0, 17);
        if (in_array($mt940Identifier, $allowedUniqueIdentifiers)) {
            return true;
        }

        return $this->isBLZAllowed($text);
    }
}
