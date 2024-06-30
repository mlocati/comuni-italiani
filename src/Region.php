<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

final class Region extends TerritoryWithChildren
{
    private GeographicalSubdivision $geographicalSubdivision;

    private ?array $provinces = null;

    public function __construct(array $data, GeographicalSubdivision $geographicalSubdivision)
    {
        parent::__construct($data);
        $this->geographicalSubdivision = $geographicalSubdivision;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getParent()
     */
    public function getParent(): Territory
    {
        return $this->getGeographicalSubdivision();
    }

    public function getGeographicalSubdivision(): GeographicalSubdivision
    {
        return $this->geographicalSubdivision;
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

    /**
     * @see \MLocati\ComuniItaliani\Region\Type
     */
    public function getType(): int
    {
        return $this->data['type'];
    }

    public function getFiscalCode(): string
    {
        return $this->data['fiscalCode'];
    }

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
     */
    public function getChildren(): array
    {
        return $this->getProvinces();
    }

    /**
     * @return \MLocati\ComuniItaliani\Province[]
     */
    public function getProvinces(): array
    {
        return $this->provinces ??= $this->buildProvinces();
    }

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
