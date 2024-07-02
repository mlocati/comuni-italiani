<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Service;

use MLocati\ComuniItaliani\Territory;
use MLocati\ComuniItaliani\TerritoryWithChildren;

/**
 * @internal
 */
trait TerritoryTrait
{
    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::isSame()
     */
    public function isSame(Territory $territory): bool
    {
        return $this->getID() === $territory->getID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::isContainedIn()
     */
    public function isContainedIn(Territory $territory): bool
    {
        return $territory instanceof TerritoryWithChildren && $territory->contains($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::isContainedIn()
     */
    public function isSameOrContainedIn(Territory $territory): bool
    {
        return $this->isSame($territory) || $this->isContainedIn($territory);
    }
}
