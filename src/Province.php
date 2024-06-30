<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

final class Province extends TerritoryWithChildren
{
    private Region $region;

    private ?array $municipalities = null;

    public function __construct(array $data, Region $region)
    {
        parent::__construct($data);
        $this->region = $region;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getParent()
     */
    public function getParent(): Territory
    {
        return $this->getRegion();
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getID()
     */
    public function getID(): string
    {
        return $this->data['id'];
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

    public function getOldID(): string
    {
        return $this->data['oldID'] ?? $this->getID();
    }

    /**
     * @see \MLocati\ComuniItaliani\Province\Type
     */
    public function getType(): int
    {
        return $this->data['type'];
    }

    public function getVehicleCode(): string
    {
        return $this->data['vehicleCode'];
    }

    public function getFiscalCode(): string
    {
        return $this->data['fiscalCode'];
    }

    public function getNuts3(): string
    {
        return $this->data['nuts3'];
    }

    /**
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
     */
    public function getChildren(): array
    {
        return $this->getMunicipalities();
    }

    /**
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
