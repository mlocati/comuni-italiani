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
}
