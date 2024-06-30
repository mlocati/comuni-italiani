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
        if (class_exists(Collator::class)) {
            return $this->sortTerritoriesByNameWithCollator($territories);
        }

        return $this->sortTerritoriesByNameWithoutCollator($territories);
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
            return $collator->compare($a->getName(), $b->getName());
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
        usort($territories, static function (Territory $a, Territory $b): int {
            return strcasecmp($a->getName(), $b->getName());
        });

        return $territories;
    }
}
