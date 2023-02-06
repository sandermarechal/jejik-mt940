<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2020 Powercloud GmbH <d.richter@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\MT940\Parser;

/**
 * GermanBank provides an abstract mt940 parser layer for german banks
 * @package Jejik\MT940\Parser
 */
abstract class GermanBank extends AbstractParser
{
    protected const IDENTIFIER_EREF = 'EREF';
    protected const IDENTIFIER_KREF = 'KREF';
    protected const IDENTIFIER_MREF = 'MREF';
    protected const IDENTIFIER_CRED = 'CRED';
    protected const IDENTIFIER_DEBT = 'DEBT';
    protected const IDENTIFIER_COAM = 'COAM';
    protected const IDENTIFIER_OAMT = 'OAMT';
    protected const IDENTIFIER_SVWZ = 'SVWZ';
    protected const IDENTIFIER_ABWA = 'ABWA';
    protected const IDENTIFIER_ABWE = 'ABWE';

    /**
     * Parse GVC for provided transaction lines
     */
    protected function gvc(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $gvcLine = $lines[1] ?? null;

        // assure gvc line
        if ($gvcLine == null) {
            return null;
        }

        // return
        return substr($gvcLine, 0, 3); // gvc has fixed 3 bytes
    }

    /**
     * Parse code for provided transaction lines
     * @todo: return code with leading N
     */
    protected function code(array $lines): ?string
    {
        // get :61: line -- it is first in provided array [:61:,:86:,....]
        $codeLine = $lines[0] ?? null;

        // assure code line
        if ($codeLine == null) {
            return null;
        }

        // match it
        preg_match('#(\d{6})(\d{4})?(R?(?:C|D))([0-9,]{1,15})N([a-zA-Z0-9]+)#', $codeLine, $match);

        // assure match
        if (!isset($match[5])) {
            return null;
        }

        // return
        return substr($match[5], 0, 3);
    }

    /**
     * Parse supplementary details
     */
    protected function supplementaryDetails(array $lines): ?string
    {
        $refLine = $lines[0] ?? null;

        $parts = preg_split("/\\r\\n|\\r|\\n/", $refLine, 2);

        return $parts[1] ?? null;
    }

    /**
     * Parse ref for provided transaction lines
     */
    protected function ref(array $lines): ?string
    {
        $refLine = $lines[0] ?? null;

        // assure ref line
        if ($refLine == null) {
            return null;
        }

        // match it
        preg_match("/(?'valuta'\d{6})(?'bookingdate'\d{4})?(?'debitcreditid'R?(?:C|D))(?'amount'[0-9,]{1,15})(?:\s*)(?'bookingkey'N[a-zA-Z0-9]{3})(?'reference'[a-zA-Z0-9+]+)(?:\/\/)*(?'bankref'[0-9a-zA-Z]{1,16})*/", $refLine, $match);

        // assure match
        return $match['reference'] ?? null;
    }

    /**
     * Parse bankRef for provided transaction lines
     */
    protected function bankRef(array $lines): ?string
    {
        $refLine = $lines[0] ?? null;

        // assure ref line
        if ($refLine == null) {
            return null;
        }

        // match it
        preg_match("/(?'valuta'\d{6})(?'bookingdate'\d{4})?(?'debitcreditid'R?(?:C|D))(?'amount'[0-9,]{1,15})(?:\s*)(?'bookingkey'N[a-zA-Z0-9]{3})(?'reference'[a-zA-Z0-9+]+)(?:\/\/)*(?'bankref'[0-9a-zA-Z]{1,16})*/", $refLine, $match);

        // assure match
        return $match['bankref'] ?? null;
    }

    /**
     * Parse txText for provided transaction lines
     */
    protected function txText(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $txTextLine = $lines[1] ?? null;

        // assure txText line
        if ($txTextLine === null) {
            return null;
        }

        // match it
        /** @var string $txTextLine */
        preg_match('#\?00([a-zA-Z0-9\-\s\.]+)#', $this->removeNewLinesFromLine($txTextLine), $match);

        // assure match
        return $match[1] ?? null;
    }

    /**
     * Parse primanota for provided transaction lines
     */
    protected function primanota(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $primanotaLine = $lines[1] ?? null;

        // assure primanota line
        if ($primanotaLine === null) {
            return null;
        }

        /** @var string $primanotaLine */
        preg_match('#\?10([a-zA-Z0-9/]{1,10})#', $this->removeNewLinesFromLine($primanotaLine), $match);

        // assure match
        return $match[1] ?? null;
    }

    /**
     * Parse extCode for provided transaction lines
     */
    protected function extCode(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $extCodeLine = $lines[1] ?? null;

        // assure extCode line
        if ($extCodeLine === null) {
            return null;
        }

        /** @var string $extCodeLine */
        preg_match('#\?34(\d{3})#', $this->removeNewLinesFromLine($extCodeLine), $match);

        return $match[1] ?? null;
    }

    /**
     */
    protected function getSubfield(string $multiUseLine, string $identifier): ?string
    {
        $multiUseLine = $this->removeNewLinesFromLine($multiUseLine);
        // extract reference line ?20 - ?29
        $foundLine = (bool) preg_match(
            '#(?<referenceLine>(\?2[0-9][^?]+)+)#',
            $multiUseLine,
            $match
        );

        $referenceLine = $match['referenceLine'] ?? null;

        if (!$foundLine || empty($referenceLine)) {
            return null;
        }

        $identifiers = [
            static::IDENTIFIER_EREF,
            static::IDENTIFIER_KREF,
            static::IDENTIFIER_MREF,
            static::IDENTIFIER_CRED,
            static::IDENTIFIER_DEBT,
            static::IDENTIFIER_COAM,
            static::IDENTIFIER_OAMT,
            static::IDENTIFIER_SVWZ,
            static::IDENTIFIER_ABWA,
            static::IDENTIFIER_ABWE,
        ];

        $regex = sprintf(
            '#(?<separator>\?2[0-9])(?<identifier>%s)\+#m',
            implode('|', $identifiers)
        );

        $splitReferenceLine = preg_split(
            $regex,
            $referenceLine,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );
        $subfields = [];

        // check if leading value is an separator: '?20<text>' '?2X' '<identifier>' '<content>'
        if (current($splitReferenceLine) !== '?20') {
            // remove first element if no separator found
            next($splitReferenceLine);
        }

        // expects : '?2X' '<identifier>' '<content>'
        do {
            $fieldIdentifier = next($splitReferenceLine) ?: 'unknown';
            $fieldContent = next($splitReferenceLine) ?: null;

            if ($fieldContent !== null) {
                $subfields[$fieldIdentifier] = preg_replace('#\?2[0-9]#', '', $fieldContent);
            }
        } while (next($splitReferenceLine) !== false);

        return $subfields[$identifier] ?? null;
    }

    /**
     * Parse eref for provided transaction lines
     */
    protected function eref(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $multiUseLine = $lines[1] ?? null;

        /** @var string|null $multiUseLine */
        return $multiUseLine
            ? $this->getSubfield($multiUseLine, static::IDENTIFIER_EREF)
            : null;
    }

    /**
     * Parse bic for provided transaction lines
     */
    protected function bic(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $bicLine = $lines[1] ?? null;

        // assure bic line
        if ($bicLine === null) {
            return null;
        }

        /** @var string $bicLine */
        preg_match('#\?30([a-zA-Z0-9]+)#', $this->removeNewLinesFromLine($bicLine), $match);

        // assure match
        return $match[1] ?? null;
    }

    /**
     * Parse iban for provided transaction lines
     */
    protected function iban(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $ibanLine = $lines[1] ?? null;

        // assure iban line
        if ($ibanLine == null) {
            return null;
        }

        // match it
        /** @var string $ibanLine */
        preg_match('#\?31([a-zA-Z0-9]+)#', $this->removeNewLinesFromLine($ibanLine), $match);

        // assure match
        return $match[1] ?? null;
    }

    /**
     * Parse accountHolder for provided transaction lines
     */
    protected function accountHolder(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $accHolderLine = $lines[1] ?? null;

        // assure acc holder line
        if ($accHolderLine == null) {
            return null;
        }

        // TODO try to match names containing ? character
        preg_match(
            '#\?32((?:[a-zA-ZöäüÖÄÜß0-9\(\)\s,\-\./\+]+(?:\?[^\?33|34])?)+)#u',
            $this->removeNewLinesFromLine($accHolderLine),
            $match
        );

        if (!isset($match[1])) {
            return null;
        }

        // additional field ?33
        /** @var string $accHolderLine */
        preg_match(
            '#\?33([a-zA-ZöäüÖÄÜß0-9\(\)\s,\-\./\+]+)#u',
            $this->removeNewLinesFromLine($accHolderLine),
            $matchAdd
        );

        if (!isset($matchAdd[1])) {
            return preg_replace('#(\?\d{2})#', '', $match[1]);
        }

        return preg_replace('#(\?\d{2})#', '', $match[1] . $matchAdd[1]);
    }

    /**
     * Parse kref for provided transaction lines
     */
    protected function kref(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $krefLine = $lines[1] ?? null;

        // pattern
        $pattern = '#K(?:\?2[1-9])?R(?:\?2[1-9])?E(?:\?2[1-9])?F(?:\?2[1-9])?\+([a-zA-ZöäüÖÄÜß0-9\./?\+\-\s,]+)(C(?:\?2[1-9])?R(?:\?2[1-9])?E(?:\?2[1-9])?D|S(?:\?2[1-9])?V(?:\?2[1-9])?W(?:\?2[1-9])?Z)#';

        /** @var string $krefLine */
        preg_match($pattern, $this->removeNewLinesFromLine($krefLine), $match);

        // assure match
        if (!isset($match[1])) {
            // try it without CRED|SVWZ info
            $pattern = '#K(?:\?2[1-9])?R(?:\?2[1-9])?E(?:\?2[1-9])?F(?:\?2[1-9])?\+([a-zA-ZöäüÖÄÜß0-9\./?\+\-\s,]+?)(\?3[0-9])#';

            // match it
            preg_match($pattern, $this->removeNewLinesFromLine($krefLine), $match);
        }

        // assure match again after avoiding CRED|SVWZ info
        if (!isset($match[1])) {
            return null;
        }

        return preg_replace('#(\?\d{0,2})#', '', $match[1]);
    }

    /**
     * Parse mref for provided transaction lines
     */
    protected function mref(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $mrefLine = $lines[1] ?? null;

        $pattern = '#M(?:\?2[1-9])?R(?:\?2[1-9])?E(?:\?2[1-9])?F(?:\?2[1-9])?\+([a-zA-ZöäüÖÄÜß0-9\./?\+\-\s,]+)(C(?:\?2[1-9])?R(?:\?2[1-9])?E(?:\?2[1-9])?D|S(?:\?2[1-9])?V(?:\?2[1-9])?W(?:\?2[1-9])?Z)#';

        preg_match($pattern, $this->removeNewLinesFromLine($mrefLine), $match);

        // assure match
        if (!isset($match[1])) {
            return null;
        }

        return preg_replace('#(\?\d{0,2})#', '', $match[1]);
    }

    /**
     * Parse cred for provided transaction lines
     */
    protected function cred(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $credLine = $lines[1] ?? null;

        $pattern = '#C(?:\?2[1-9])?R(?:\?2[1-9])?E(?:\?2[1-9])?D(?:\?2[1-9])?\+([a-zA-ZöäüÖÄÜß0-9\./?\+\-\s,]+)S(?:\?2[1-9])?V(?:\?2[1-9])?W(?:\?2[1-9])?Z#';

        // match it
        preg_match($pattern, $this->removeNewLinesFromLine($credLine), $match);

        // assure match
        if (!isset($match[1])) {
            return null;
        }

        return preg_replace('#(\?\d{0,2})#', '', $match[1]);
    }

    /**
     * Parse svwz for provided transaction lines
     */
    protected function svwz(array $lines): ?string
    {
        // get :86: line -- it is second in provided array [:61:,:86:,....]
        $svwzLine = $lines[1] ?? null;

        $pattern = "/(S(?:\?2[1-9])?V(?:\?2[1-9])?W(?:\?2[1-9])?Z(?:\?2[1-9])?\+)(?:\?(?:2[1-9]))?(?'SVWZ'.*)(?:\?30)/";

        /** @var string $svwzLine */
        preg_match($pattern, $this->removeNewLinesFromLine($svwzLine), $match);

        // assure match
        if (!isset($match['SVWZ'])) {
            return null;
        }

        return preg_replace('/(\?2[1-9])/', '', $match['SVWZ']);
    }

    /**
     * Remove all new lines and carriage returns from provided input line
     */
    private function removeNewLinesFromLine(string $stringLine): string
    {
        return str_replace(["\n", "\r", "\r\n"], '', $stringLine);
    }
}
