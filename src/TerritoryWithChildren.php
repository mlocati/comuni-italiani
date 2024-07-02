<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

/**
 * Interface implemented by territories that can have child territories (Geographical Subdivisions, Regions, Provinces).
 */
interface TerritoryWithChildren extends Territory
{
    /**
     * Get all the direct child territories of this territory.
     *
     * @return \MLocati\ComuniItaliani\Territory[]
     */
    public function getChildren(): array;

    /**
     * Check if this territory contains another territory.
     */
    public function contains(Territory $territory): bool;

    /**
     * Check if this territory contains another territory, or if it's the same as this.
     */
    public function isSameOrContains(Territory $territory): bool;
}
