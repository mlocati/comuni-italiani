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
     * Check if a territory instance is the same as this.
     */
    public function isSame(Territory $territory): bool;

    /**
     * Check if this territory is a child/grandchild/great-grandchild/... of another territory.
     */
    public function isContainedIn(Territory $territory): bool;

    /**
     * Check if this territory is a child/grandchild/great-grandchild/... of another territory, of if they are the same
     */
    public function isSameOrContainedIn(Territory $territory): bool;

    /**
     * Get a string representation that uniquely represents the territory.
     */
    public function __toString(): string;
}
