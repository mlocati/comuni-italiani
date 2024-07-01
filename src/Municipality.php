<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

final class Municipality implements Territory
{
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
    public function getParent(): Territory
    {
        return $this->getProvince();
    }

    public function getProvince(): Province
    {
        return $this->province;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getID()
     */
    public function getID(): string
    {
        return $this->getProvince()->getOldID() . $this->data['id'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getName()
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    public function getNameIT(): string
    {
        return $this->data['nameIT'] ?? '';
    }

    public function getNameForeign(): string
    {
        return $this->data['nameForeign'] ?? '';
    }

    public function isRegionalCapital(): bool
    {
        return $this->data['isRegionalCapital'] ?? false;
    }

    public function isProvinceCapital(): bool
    {
        return $this->data['isProvinceCapital'] ?? false;
    }

    public function getCadastralCode(): string
    {
        return $this->data['cadastralCode'];
    }

    public function getFiscalCode(): string
    {
        return $this->data['fiscalCode'] ?? '';
    }

    public function getNuts1(): string
    {
        return $this->data['nuts1'] ?? $this->getProvince()->getRegion()->getGeographicalSubdivision()->getNuts1();
    }

    public function getNuts2(): string
    {
        return $this->data['nuts2'] ?? $this->getProvince()->getRegion()->getNuts2();
    }

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
