<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Service;

use MLocati\ComuniItaliani\Territory;
use MLocati\ComuniItaliani\TerritoryWithChildren;

/**
 * @internal
 */
trait TerritoryWithChildrenTrait
{
    use TerritoryTrait;

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\TerritoryWithChildren::contains()
     */
    public function contains(Territory $territory): bool
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof TerritoryWithChildren) {
                if ($child->isSameOrContains($territory)) {
                    return true;
                }
            } elseif ($child->isSame($territory)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\TerritoryWithChildren::isSameOrContains()
     */
    public function isSameOrContains(Territory $territory): bool
    {
        return $this->isSame($territory) || $this->contains($territory);
    }
}
