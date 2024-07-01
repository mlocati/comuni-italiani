<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

class Finder
{
    protected Factory $factory;

    public function __construct(?Factory $factory = null)
    {
        $this->factory = $factory ?? new Factory();
    }

    /**
     * Find a territory (Geographical Subdivision, Region, Province, or Municipality) given its ID.
     *
     * @param int|string|mixed $id int for Geographical Subdivision, string for other territory types.
     */
    public function getTerritoryByID($id): ?Territory
    {
        switch (gettype($id)) {
            case 'integer':
                return $this->getGeographicalSubdivisionByID($id);
            case 'string':
                switch (strlen($id)) {
                    case 2:
                        return $this->getRegionByID($id);
                    case 3:
                        return $this->getProvinceByID($id);
                    case 6:
                        return $this->getMunicipalityByID($id);
                }
                break;
        }

        return null;
    }

    /**
     * Find a Geographical Subdivision given its ID
     */
    public function getGeographicalSubdivisionByID(int $id): ?GeographicalSubdivision
    {
        foreach ($this->factory->getGeographicalSubdivisions() as $geographicalSubdivision) {
            if ($geographicalSubdivision->getID() === $id) {
                return $geographicalSubdivision;
            }
        }

        return null;
    }

    /**
     * Find a Region given its ID
     */
    public function getRegionByID(string $id): ?Region
    {
        if (!preg_match('/^[0-9]{2}$/', $id)) {
            return null;
        }
        foreach ($this->factory->getGeographicalSubdivisions() as $geographicalSubdivisions) {
            foreach ($geographicalSubdivisions->getRegions() as $region) {
                if ($region->getID() === $id) {
                    return $region;
                }
            }
        }

        return null;
    }

    /**
     * Find a Province/UTS given its ID
     *
     * @param bool $oldIDToo look for $in in historical Province codes too?
     */
    public function getProvinceByID(string $id, bool $oldIDToo = false): ?Province
    {
        if (!preg_match('/^[0-9]{3}$/', $id)) {
            return null;
        }
        foreach ($this->factory->getGeographicalSubdivisions() as $geographicalSubdivisions) {
            foreach ($geographicalSubdivisions->getRegions() as $region) {
                foreach ($region->getProvinces() as $province) {
                    foreach ($province->getMunicipalities() as $municipality) {
                        if ($province->getID() === $id) {
                            return $province;
                        }
                        if ($oldIDToo && $province->getOldID() === $id) {
                            return $province;
                        }
                    }
                }
            }
        }

        return null;
    }


    /**
     * Find a Municipality given its ID
     *
     * @param bool $oldIDToo look for $in in historical Province codes too?
     */
    public function getMunicipalityByID(string $id): ?Municipality
    {
        if (!preg_match('/^[0-9]{6}$/', $id)) {
            return null;
        }
        foreach ($this->factory->getGeographicalSubdivisions() as $geographicalSubdivisions) {
            foreach ($geographicalSubdivisions->getRegions() as $region) {
                foreach ($region->getProvinces() as $province) {
                    foreach ($province->getMunicipalities() as $municipality) {
                        if ($municipality->getID() === $id) {
                            return $municipality;
                        }
                    }
                }
            }
        }

        return null;
    }
}
