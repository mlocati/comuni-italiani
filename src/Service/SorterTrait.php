<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Service;

use Collator;
use MLocati\ComuniItaliani\Territory;

trait SorterTrait
{
    /**
     * @param \MLocati\ComuniItaliani\Territory[] $territories
     *
     * @return \MLocati\ComuniItaliani\Territory[]
     */
    protected function sortTerritoriesByName(array $territories): array
    {
        return class_exists(Collator::class) ? $this->sortTerritoriesByNameWithCollator($territories) : $this->sortTerritoriesByNameWithoutCollator($territories);
    }

    /**
     * @param \MLocati\ComuniItaliani\Territory[] $territories
     *
     * @return \MLocati\ComuniItaliani\Territory[]
     */
    protected function sortTerritoriesByNameWithCollator(array $territories): array
    {
        $collator = new Collator('it_IT');
        $collator->setStrength(Collator::SECONDARY);

        usort($territories, static function (Territory $a, Territory $b) use ($collator): int {
            return $collator->compare((string) $a, (string) $b);
        });

        return $territories;
    }

    /**
     * @param \MLocati\ComuniItaliani\Territory[] $territories
     *
     * @return \MLocati\ComuniItaliani\Territory[]
     */
    protected function sortTerritoriesByNameWithoutCollator(array $territories): array
    {
        $map = static::getMultibyteToAsciiMap();
        usort($territories, static function (Territory $a, Territory $b) use ($map): int {
            return strcasecmp(
                strtr((string) $a, $map),
                strtr((string) $b, $map)
            );
        });

        return $territories;
    }

    protected static function getMultibyteToAsciiMap(): array
    {
        return [
            "\u{e0}" => 'a',
            "\u{e2}" => 'a',
            "\u{e7}" => 'c',
            "\u{10d}" => 'c',
            "\u{e8}" => 'e',
            "\u{e9}" => 'e',
            "\u{ea}" => 'e',
            "\u{ec}" => 'i',
            "\u{f2}" => 'o',
            "\u{f6}" => 'o',
            "\u{f4}" => 'o',
            "\u{f9}" => 'u',
            "\u{fc}" => 'u',
            "\u{df}" => 'ss',
            "\u{17e}" => 'z',
        ];
    }
}
