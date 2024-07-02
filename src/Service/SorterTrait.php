<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Service;

use Collator;
use MLocati\ComuniItaliani\Territory;

/**
 * @internal
 */
trait SorterTrait
{
    use MultibyteTrait;

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
}
