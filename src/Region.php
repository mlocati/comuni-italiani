<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

/**
 * Represents a Region (regione)
 */
final class Region implements TerritoryWithChildren
{
    use Service\TerritoryWithChildrenTrait;

    private array $data;

    private GeographicalSubdivision $geographicalSubdivision;

    private ?array $provinces = null;

    public function __construct(array $data, GeographicalSubdivision $geographicalSubdivision)
    {
        $this->data = $data;
        $this->geographicalSubdivision = $geographicalSubdivision;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getParent()
     * @see \MLocati\ComuniItaliani\Region::getGeographicalSubdivision()
     */
    public function getParent(): GeographicalSubdivision
    {
        return $this->getGeographicalSubdivision();
    }

    /**
     * Get the Geographical Subdivision this Region belongs to.
     */
    public function getGeographicalSubdivision(): GeographicalSubdivision
    {
        return $this->geographicalSubdivision;
    }

    /**
     * Get the Region Istat ID ("codice Istat della regione").
     *
     * @see \MLocati\ComuniItaliani\Territory::getID()
     */
    public function getID(): string
    {
        return $this->data['id'];
    }

    /**
     * Get the Region name.
     *
     * @see \MLocati\ComuniItaliani\Territory::getName()
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Get the Region type.
     *
     * @see \MLocati\ComuniItaliani\Region\Type
     */
    public function getType(): int
    {
        return $this->data['type'];
    }

    /**
     * Get the Fiscal Code ("codice fiscale") assigned by the Agenzia delle Entrate in order to uniquely identify a legal entity.
     */
    public function getFiscalCode(): string
    {
        return $this->data['fiscalCode'];
    }

    /**
     * Get the second level of the current Nomenclature of Territorial Units for Statistics (NUTS2).
     */
    public function getNuts2(): string
    {
        return $this->data['nuts2'] ?? '';
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
     * @see \MLocati\ComuniItaliani\Region::getProvinces()
     */
    public function getChildren(): array
    {
        return $this->getProvinces();
    }

    /**
     * Get all the Provinces/UTS belonging to this Region, sorted by name
     *
     * @return \MLocati\ComuniItaliani\Province[]
     */
    public function getProvinces(): array
    {
        return $this->provinces ??= $this->buildProvinces();
    }

    /**
     * Get the Capital Municipality of this Region ("capoluogo di regione").
     */
    public function getCapital(): Municipality
    {
        foreach ($this->getProvinces() as $province) {
            foreach ($province->getMunicipalities() as $municipality) {
                if ($municipality->isRegionalCapital()) {
                    return $municipality;
                }
            }
        }
    }

    /**
     * @return \MLocati\ComuniItaliani\Province[]
     */
    private function buildProvinces(): array
    {
        $result = array_map(
            function (array $data): Province {
                return new Province($data, $this);
            },
            $this->data['children']
        );

        unset($this->data['children']);

        return $result;
    }
}
