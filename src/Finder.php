<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

use Generator;

class Finder
{
    use Service\SorterTrait;

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
        foreach ($this->listGeographicalSubdivisions() as $geographicalSubdivision) {
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
        foreach ($this->listRegions() as $region) {
            if ($region->getID() === $id) {
                return $region;
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
        foreach ($this->listProvinces() as $province) {
            if ($province->getID() === $id) {
                return $province;
            }
            if ($oldIDToo && $province->getOldID() === $id) {
                return $province;
            }
        }

        return null;
    }

    public function getProvinceByVehicleCode(string $vehicleCode): ?Province
    {
        if (!preg_match('/^[A-Za-z]{2}$/', $vehicleCode)) {
            return null;
        }
        $vehicleCode = strtoupper($vehicleCode);
        foreach ($this->listProvinces() as $province) {
            if ($province->getVehicleCode() === $vehicleCode) {
                return $province;
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
        foreach ($this->listMunicipalities() as $municipality) {
            if ($municipality->getID() === $id) {
                return $municipality;
            }
        }

        return null;
    }

    /**
     * @param bool $allowMiddle Set to true to look for in the moddle of the words too.
     *
     * @return \MLocati\ComuniItaliani\GeographicalSubdivision[]
     */
    public function findGeographicalSubdivisionsByName(string $name, bool $allowMiddle = false): array
    {
        return $this->findByName($name, $this->listGeographicalSubdivisions(), $allowMiddle, false);
    }

    /**
     * @param bool $allowMiddle Set to true to look for in the moddle of the words too.
     * @param \MLocati\ComuniItaliani\GeographicalSubdivision|null $restrictTo only look for regions contained in a specific territory
     *
     * @return \MLocati\ComuniItaliani\Region[]
     */
    public function findRegionsByName(string $name, bool $allowMiddle = false, ?TerritoryWithChildren $restrictTo = null): array
    {
        return $this->findByName($name, $this->listRegions($restrictTo), $allowMiddle, true);
    }

    /**
     * @param bool $allowMiddle Set to true to look for in the moddle of the words too.
     * @param \MLocati\ComuniItaliani\GeographicalSubdivision|\MLocati\ComuniItaliani\Region|null $restrictTo only look for regions contained in a specific territory
     * Only meaning
     *
     * @return \MLocati\ComuniItaliani\Province[]
     */
    public function findProvincesByName(string $name, bool $allowMiddle = false, ?TerritoryWithChildren $restrictTo = null): array
    {
        return $this->findByName($name, $this->listProvinces($restrictTo), $allowMiddle, true);
    }

    /**
     * @param bool $allowMiddle Set to true to look for in the moddle of the words too.
     * @param \MLocati\ComuniItaliani\TerritoryWithChildren|null $restrictTo only look for regions contained in a specific territory
     *
     * @return \MLocati\ComuniItaliani\Municipality[]
     */
    public function findMunicipalitiesByName(string $name, bool $allowMiddle = false, ?TerritoryWithChildren $restrictTo = null): array
    {
        return $this->findByName($name, $this->listMunicipalities($restrictTo), $allowMiddle, true);
    }

    /**
     * @param \Generator<\MLocati\ComuniItaliani\Territory> $lister
     *
     * @return \MLocati\ComuniItaliani\Territory[]
     */
    protected function findByName(string $name, Generator $lister, bool $allowMiddle, bool $sort): array
    {
        if (($wantedWords = $this->extractWords($name)) === []) {
            return [];
        }
        $result = [];
        foreach ($lister as $territory) {
            $territoryWords = $this->extractWords($territory->getName());
            if ($this->matchesWords($wantedWords, $territoryWords, $allowMiddle)) {
                $result[] = $territory;
            }
        }

        return $sort ? $this->sortTerritoriesByName($result) : $result;
    }

    /**
     * @return \Generator<\MLocati\ComuniItaliani\GeographicalSubdivision>
     */
    protected function listGeographicalSubdivisions(): Generator
    {
        foreach ($this->factory->getGeographicalSubdivisions() as $geographicalSubdivisions) {
            yield $geographicalSubdivisions;
        }
    }

    /**
     * @return \Generator<\MLocati\ComuniItaliani\Region>
     */
    protected function listRegions(?TerritoryWithChildren $restrictTo = null): Generator
    {
        if ($restrictTo instanceof GeographicalSubdivision) {
            $geographicalSubdivisions = [$restrictTo];
        } elseif ($restrictTo === null) {
            $geographicalSubdivisions = $this->listGeographicalSubdivisions();
        } else {
            return;
        }
        foreach ($geographicalSubdivisions as $geographicalSubdivision) {
            foreach ($geographicalSubdivision->getRegions() as $region) {
                yield $region;
            }
        }
    }

    /**
     * @return \Generator<\MLocati\ComuniItaliani\Province>
     */
    protected function listProvinces(?TerritoryWithChildren $restrictTo = null): Generator
    {
        if ($restrictTo instanceof Region) {
            $regions = [$restrictTo];
        } else {
            $regions = $this->listRegions($restrictTo);
        }
        foreach ($regions as $region) {
            foreach ($region->getProvinces() as $province) {
                yield $province;
            }
        }
    }

    /**
     * @return \Generator<\MLocati\ComuniItaliani\Municipality>
     */
    protected function listMunicipalities(?TerritoryWithChildren $restrictTo = null): Generator
    {
        if ($restrictTo instanceof Province) {
            $provinces = [$restrictTo];
        } else {
            $provinces = $this->listProvinces($restrictTo);
        }
        foreach ($provinces as $province) {
            foreach ($province->getMunicipalities() as $municipality) {
                yield $municipality;
            }
        }
    }

    /**
     * @return string[]
     */
    protected function extractWords(string $name): array
    {
        $name = strtr($name, self::getMultibyteToAsciiMap());
        $name = strtolower($name);
        $words = preg_split('/[^a-z]+/', $name, -1, PREG_SPLIT_NO_EMPTY);

        return $words;
    }

    /**
     * @param string[] $wantedWords
     * @param string[] $territoryWords
     */
    protected function matchesWords(array $wantedWords, array $territoryWords, bool $allowMiddle): bool
    {
        $index = -1;
        foreach ($wantedWords as $wantedWord) {
            $index = $this->matchesWord($wantedWord, $territoryWords, $allowMiddle, $index);
            if ($index === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string[] $territoryWords
     */
    protected function matchesWord(string $wantedWord, array $territoryWords, bool $allowMiddle, int $afterIndex): ?int
    {
        foreach ($territoryWords as $index => $territoryWord) {
            if ($index > $afterIndex) {
                $found = strpos($territoryWord, $wantedWord);
                if ($found === 0 || ($allowMiddle && $found !== false)) {
                    return $index;
                }
            }
        }

        return null;
    }
}
