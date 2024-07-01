<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Service;

trait MultibyteTrait
{
    protected static function getMultibyteToAsciiMap(): array
    {
        return [
            "\u{e0}" => 'a', // à
            "\u{c0}" => 'A', // À
            "\u{e2}" => 'a', // â
            "\u{c2}" => 'A', // Â
            "\u{e7}" => 'c', // ç
            "\u{c7}" => 'C', // Ç
            "\u{10d}" => 'c', // č
            "\u{10c}" => 'C', // Č
            "\u{e8}" => 'e', // è
            "\u{c8}" => 'E', // È
            "\u{e9}" => 'e', // é
            "\u{c9}" => 'E', // É
            "\u{ea}" => 'e', // ê
            "\u{ca}" => 'E', // Ê
            "\u{ec}" => 'i', // ì
            "\u{cc}" => 'I', // Ì
            "\u{f2}" => 'o', // ò
            "\u{d2}" => 'O', // Ò
            "\u{f6}" => 'o', // ö
            "\u{d6}" => 'O', // Ö
            "\u{f4}" => 'o', // ô
            "\u{d4}" => 'O', // Ô
            "\u{f9}" => 'u', // ù
            "\u{d9}" => 'U', // Ù
            "\u{fc}" => 'u', // ü
            "\u{dc}" => 'U', // Ü
            "\u{df}" => 'ss', // ß
            "\u{17e}" => 'z', // ž
            "\u{17d}" => 'Z', // Ž
        ];
    }
}
