<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2025 Lars Riße - chargecloud GmbH
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\MT940\Parser;

/**
 * Class RaiffeisenKaernten
 *
 * @package Jejik\MT940\Parser
 */
class RaiffeisenKaernten extends GermanBank
{
    /** @inheritDoc */
    public function getAllowedBLZ(): array
    {
        return ['AT39364'];
    }

    /** @inheritDoc */
    public function accept(string $text): bool
    {
        return $this->isBLZAllowed($text);
    }

    /** @inheritDoc */
    public function isBLZAllowed($text): bool
    {
        $this->checkCRLF($text);

        $account = $this->getLine('25', $text);

        if ($account === null) {
            return false;
        }

        $accountExploded = explode('/', $account);

        return isset($accountExploded[2]) && \in_array($accountExploded[2], $this->getAllowedBLZ(), true);
    }

    /** @inheritDoc */
    protected function accountNumber(string $text): ?string
    {
        $accountNumber = parent::accountNumber($text);

        return str_replace(['//', '/EUR'], '', $accountNumber);
    }
}
