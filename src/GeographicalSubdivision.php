<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

/**
 * Represents a Geographical Subdivision (ripartizione geografica)
 */
final class GeographicalSubdivision implements TerritoryWithChildren
{
    private array $data;

    private ?array $regions = null;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getParent()
     *
     * @return NULL
     */
    public function getParent(): ?TerritoryWithChildren
    {
        return null;
    }

    /**
     * Get the Geographical Subdivision Istat ID ("codice Istat della Ripartizione geografica").
     *
     * @see \MLocati\ComuniItaliani\Territory::getID()
     */
    public function getID(): int
    {
        return $this->data['id'];
    }

    /**
     * Get the Geographical Subdivision name.
     *
     * @see \MLocati\ComuniItaliani\Territory::getName()
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Get the first level of the current Nomenclature of Territorial Units for Statistics (NUTS1).
     */
    public function getNuts1(): string
    {
        return $this->data['nuts1'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::__toString()
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\TerritoryWithChildren::getChildren()
     * @see \MLocati\ComuniItaliani\GeographicalSubdivision::getRegions()
     */
    public function getChildren(): array
    {
        return $this->getRegions();
    }

    /**
     * Get all the Regions belonging to this Geographical Subdivision, sorted by name.
     *
     * @return \MLocati\ComuniItaliani\Region[]
     */
    public function getRegions(): array
    {
        return $this->regions ??= $this->buildRegions();
    }

    /**
     * @return \MLocati\ComuniItaliani\Region[]
     */
    private function buildRegions(): array
    {
        $result = array_map(
            function (array $data): Region {
                return new Region($data, $this);
            },
            $this->data['children']
        );

        unset($this->data['children']);

        return $result;
    }
}
