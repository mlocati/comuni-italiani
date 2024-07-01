<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

class Factory
{
    use Service\SorterTrait;

    /**
     * @var \MLocati\ComuniItaliani\GeographicalSubdivision[]|null
     */
    private ?array $geographicalSubdivisions = null;

    /**
     * Get all the Geographical Subdivisions, sorted by geographical location).
     *
     * @return \MLocati\ComuniItaliani\GeographicalSubdivision[]
     */
    public function getGeographicalSubdivisions(): array
    {
        return $this->geographicalSubdivisions ??= $this->buildGeographicalSubdivisions();
    }

    /**
     * Get all the Regions, sorted by name.
     *
     * @return \MLocati\ComuniItaliani\Region[]
     */
    public function getRegions(): array
    {
        $result = [];
        foreach ($this->getGeographicalSubdivisions() as $geographicalSubdivision) {
            $result = array_merge($result, $geographicalSubdivision->getRegions());
        }

        return $this->sortTerritoriesByName($result);
    }

    /**
     * Get all the Provinces, sorted by name.
     *
     * @return \MLocati\ComuniItaliani\Province[]
     */
    public function getProvinces(): array
    {
        $result = [];
        foreach ($this->getRegions() as $region) {
            $result = array_merge($result, $region->getProvinces());
        }

        return $this->sortTerritoriesByName($result);
    }

    /**
     * Get all the Municipalities, sorted by name.
     *
     * @return \MLocati\ComuniItaliani\Municipality[]
     */
    public function getMunicipalities(): array
    {
        $result = [];
        foreach ($this->getProvinces() as $province) {
            $result = array_merge($result, $province->getMunicipalities());
        }

        return $this->sortTerritoriesByName($result);
    }

    /**
     * @return \MLocati\ComuniItaliani\GeographicalSubdivision[]
     */
    protected function buildGeographicalSubdivisions(): array
    {
        $result = [];
        foreach ($this->getTerritoryData() as $data) {
            $result[] = new GeographicalSubdivision($data);
        }

        return $result;
    }

    protected function getTerritoryData(): array
    {
        return require __DIR__ . '/data/geographical-subdivisions.php';
    }
}
