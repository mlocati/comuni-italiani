<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

/**
 * Represents a Province (provincia) or a supra-municipal territorial units (UTS - unità territoriale sovracomunale)
 */
final class Province implements TerritoryWithChildren
{
    private array $data;

    private Region $region;

    private ?array $municipalities = null;

    public function __construct(array $data, Region $region)
    {
        $this->data = $data;
        $this->region = $region;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getParent()
     * @see \MLocati\ComuniItaliani\Province::getRegion()
     */
    public function getParent(): Region
    {
        return $this->getRegion();
    }

    /**
     * Get the Region this Province/UTS belongs to.
     */
    public function getRegion(): Region
    {
        return $this->region;
    }

    /**
     * Get the Province/UTS Istat ID ("codice Istat della provincia o UTS").
     *
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getID()
     */
    public function getID(): string
    {
        return $this->data['id'];
    }

    /**
     * Get the Province/UTS name.
     *
     * @see \MLocati\ComuniItaliani\Territory::getName()
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Get historical Province code ("Codice Provincia (storico)")
     *
     * @return string
     */
    public function getOldID(): string
    {
        return $this->data['oldID'] ?? $this->getID();
    }

    /**
     * Get the type of the Province/UTS.
     *
     * @see \MLocati\ComuniItaliani\Province\Type
     */
    public function getType(): int
    {
        return $this->data['type'];
    }

    /**
     * Get the vehicle code ("Sigla automobilistica").
     */
    public function getVehicleCode(): string
    {
        return $this->data['vehicleCode'];
    }

    /**
     * Get the Fiscal Code ("codice fiscale") assigned by the Agenzia delle Entrate in order to uniquely identify a legal entity.
     */
    public function getFiscalCode(): string
    {
        return $this->data['fiscalCode'];
    }

    /**
     * Get the third level of the current Nomenclature of Territorial Units for Statistics (NUTS3).
     */
    public function getNuts3(): string
    {
        return $this->data['nuts3'];
    }

    /**
     * Get the Capital Municipalities of this Province/UTS ("capoluogo di provincia"), sorted by name.
     *
     * We always have exactly one capital, except very few cases when we have more than one.
     *
     * @return \MLocati\ComuniItaliani\Municipality[]
     */
    public function getCapitals(): array
    {
        $result = [];
        foreach ($this->getMunicipalities() as $municipality) {
            if ($municipality->isProvinceCapital()) {
                $result[] = $municipality;
            }
        }

        return $result;
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
     * @see \MLocati\ComuniItaliani\Region::getMunicipalities()
     */
    public function getChildren(): array
    {
        return $this->getMunicipalities();
    }

    /**
     * Get all the Municipalities belonging to this Region, sorted by name.
     *
     * @return \MLocati\ComuniItaliani\Municipality[]
     */
    public function getMunicipalities(): array
    {
        return $this->municipalities ??= $this->buildMunicipalities();
    }

    /**
     * @return \MLocati\ComuniItaliani\Municipality[]
     */
    private function buildMunicipalities(): array
    {
        $result = array_map(
            function (array $data): Municipality {
                return new Municipality($data, $this);
            },
            $this->data['children']
        );

        unset($this->data['children']);

        return $result;
    }
}
