<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

/**
 * Interface implemented by every kind of territory (Geographical Subdivisions, Regions, Provinces, Municipalities).
 */
interface Territory
{
    /**
     * Get the unique identifier of this territory.
     * @return string|int
     */
    public function getID();

    /**
     * Get the parent territory.
     * Returns NULL if there's no parent territory (that only occurs for Geographical Subdivisions).
     */
    public function getParent(): ?TerritoryWithChildren;

    /**
     * Get the name of the territory.
     */
    public function getName(): string;

    /**
     * Get a string representation that uniquely represents the territory.
     */
    public function __toString(): string;
}
