<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Build;

use Collator;
use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @internal
 */
final readonly class Builder
{
    private Fetcher $fetcher;

    public function __construct(?Fetcher $fetcher = null, ?CacheInterface $cache = null)
    {
        $this->fetcher = $fetcher ?? new Fetcher(cache: $cache);
    }

    public function buildData(?DateTimeInterface $date = null): array
    {
        $date ??= new DateTimeImmutable();
        $geographicalSubdivisions = $this->collect($date);

        return $this->serialize($geographicalSubdivisions);
    }

    /**
     * @return \MLocati\ComuniItaliani\Build\GeographicalSubdivision[]
     */
    private function collect(DateTimeInterface $date): array
    {
        $geographicalSubdivisions = [];
        foreach ($this->fetcher->listGeographicalSubdivisions($date) as $data) {
            $geographicalSubdivision = new GeographicalSubdivision($data);
            if (isset($geographicalSubdivisions[$geographicalSubdivision->id])) {
                throw new RuntimeException('Duplicated Geographical Subdivision');
            }
            $geographicalSubdivisions[$geographicalSubdivision->id] = $geographicalSubdivision;
        }
        foreach ($this->fetcher->listRegions($date) as $data) {
            $geographicalSubdivision = $geographicalSubdivisions[$data->COD_RIP] ?? null;
            if ($geographicalSubdivision === null) {
                throw new RuntimeException('Missing Geographical Subdivision');
            }
            $geographicalSubdivision->checkRegionData($data);
            $region = new Region($geographicalSubdivision, $data);
            if (isset($geographicalSubdivision->regions[(int) $region->id])) {
                throw new RuntimeException('Duplicated Region');
            }
            $geographicalSubdivision->regions[(int) $region->id] = $region;
        }
        foreach ($this->fetcher->listProvinces($date) as $data) {
            $geographicalSubdivision = $geographicalSubdivisions[$data->COD_RIP] ?? null;
            if ($geographicalSubdivision === null) {
                throw new RuntimeException('Missing Geographical Subdivision');
            }
            $geographicalSubdivision->checkProvinceData($data);
            $region = $geographicalSubdivision->regions[(int) $data->COD_REG] ?? null;
            if ($region === null) {
                throw new RuntimeException('Missing Region');
            }
            $region->checkProvinceData($data);
            $province = new Province($region, $data);
            if (isset($region->provinces[(int) $province->id])) {
                throw new RuntimeException('Duplicated Region');
            }
            $region->provinces[(int) $province->id] = $province;
        }
        foreach ($this->fetcher->listMunicipalities($date) as $data) {
            $geographicalSubdivision = $geographicalSubdivisions[$data->COD_RIP] ?? null;
            if ($geographicalSubdivision === null) {
                throw new RuntimeException('Missing Geographical Subdivision');
            }
            $geographicalSubdivision->checkMunicipalityData($data);
            $region = $geographicalSubdivision->regions[(int) $data->COD_REG] ?? null;
            if ($region === null) {
                throw new RuntimeException('Missing Region');
            }
            $region->checkMunicipalityData($data);
            $province = $region->provinces[(int) $data->COD_UTS] ?? null;
            if ($province === null) {
                throw new RuntimeException('Missing Province');
            }
            $province->checkMunicipalityData($data);
            $municipality = new Municipality($province, $data);
            if (isset($province->municipalities[(int) $municipality->id])) {
                throw new RuntimeException('Duplicated Municipality');
            }
            $province->municipalities[(int) $municipality->id] = $municipality;
        }

        return $geographicalSubdivisions;
    }

    /**
     * @param \MLocati\ComuniItaliani\Build\GeographicalSubdivision[] $geographicalSubdivisions
     */
    private function serialize(array $geographicalSubdivisions): array
    {
        $collator = new Collator('it_IT');
        $collator->setStrength(Collator::SECONDARY);
        usort(
            $geographicalSubdivisions,
            static fn(GeographicalSubdivision $a, GeographicalSubdivision $b): int => $a->id - $b->id
        );

        return array_map(static fn(GeographicalSubdivision $item) => $item->serialize($collator), $geographicalSubdivisions);
    }
}
