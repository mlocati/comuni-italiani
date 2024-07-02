<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

/**
 * Represents a Municipality (comune)
 */
final class Municipality implements Territory
{
    use Service\TerritoryTrait;

    private array $data;

    private Province $province;

    public function __construct(array $data, Province $province)
    {
        $this->data = $data;
        $this->province = $province;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getParent()
     */
    public function getParent(): Province
    {
        return $this->getProvince();
    }

    /**
     * Get the Province this Municipality belongs to.
     */
    public function getProvince(): Province
    {
        return $this->province;
    }

    /**
     * Get the Municipality Istat ID ("codice Istat del comune").
     *
     * @see \MLocati\ComuniItaliani\Territory::getID()
     */
    public function getID(): string
    {
        return $this->getProvince()->getOldID() . $this->data['id'];
    }

    /**
     * Get the Municipality name.
     *
     * @see \MLocati\ComuniItaliani\Territory::getName()
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Get the Municipality Italian name, if the municipality has both an Italian and a foreign (eg German or French) name.
     *
     * Returns an empty string if it's the same as the value returned by getName().
     */
    public function getNameIT(): string
    {
        return $this->data['nameIT'] ?? '';
    }

    /**
     * Get the Municipality foreign name, if the municipality has both an Italian and a foreign (eg German or French) name.
     *
     * Returns an empty string if it's the same as the value returned by getName().
     */
    public function getNameForeign(): string
    {
        return $this->data['nameForeign'] ?? '';
    }

    /**
     * Is this municipality the capital of the Region it belongs to?
     */
    public function isRegionalCapital(): bool
    {
        return $this->data['isRegionalCapital'] ?? false;
    }

    /**
     * Is this municipality the capital of the Province/ITS it belongs to?
     */
    public function isProvinceCapital(): bool
    {
        return $this->data['isProvinceCapital'] ?? false;
    }

    /**
     * Get the cadastral code ("codice catastale" or "codice catasto")
     */
    public function getCadastralCode(): string
    {
        return $this->data['cadastralCode'];
    }

    /**
     * Get the Fiscal Code ("codice fiscale") assigned by the Agenzia delle Entrate in order to uniquely identify a legal entity.
     */
    public function getFiscalCode(): string
    {
        return $this->data['fiscalCode'] ?? '';
    }

    /**
     * Get the first level of the current Nomenclature of Territorial Units for Statistics (NUTS1).
     */
    public function getNuts1(): string
    {
        return $this->data['nuts1'] ?? $this->getProvince()->getRegion()->getGeographicalSubdivision()->getNuts1();
    }

    /**
     * Get the second level of the current Nomenclature of Territorial Units for Statistics (NUTS2).
     */
    public function getNuts2(): string
    {
        return $this->data['nuts2'] ?? $this->getProvince()->getRegion()->getNuts2();
    }

    /**
     * Get the third level of the current Nomenclature of Territorial Units for Statistics (NUTS3).
     */
    public function getNuts3(): string
    {
        return $this->data['nuts3'] ?? $this->getProvince()->getNuts3();
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::__toString()
     */
    public function __toString(): string
    {
        return $this->getName() . ' (' . $this->getProvince()->getVehicleCode() . ')';
    }
}
