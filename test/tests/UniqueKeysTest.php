<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test;

use MLocati\ComuniItaliani\Test\Service\TerritoryTestCase;

class UniqueKeysTest extends TerritoryTestCase
{
    public function provideGeographicalSubdivisionCases(): array
    {
        return [
            ['getID'],
            ['getName'],
            ['getNuts1'],
        ];
    }

    /**
     * @dataProvider provideGeographicalSubdivisionCases
     */
    public function testGeographicalSubdivisionKey(string $getter): void
    {
        $keys = [];
        foreach ($this->provideGeographicalSubdivisions() as [$geographicalSubdivision]) {
            $key = $geographicalSubdivision->{$getter}();
            $this->assertNotSame('', $key, "{$geographicalSubdivision->getID()} {$geographicalSubdivision}");
            $this->assertNotNull($key, "{$geographicalSubdivision->getID()} {$geographicalSubdivision}");
            $this->assertSame(false, in_array($key, $keys, true), "{$geographicalSubdivision->getID()} {$geographicalSubdivision}");
            $keys[] = $key;
        }
    }

    public function provideRegionCases(): array
    {
        return [
            ['getID'],
            ['getName'],
            ['getFiscalCode'],
            ['getNuts2', [
                '04', // Trentino-Alto Adige/Südtirol
            ]],
        ];
    }

    /**
     * @dataProvider provideRegionCases
     */
    public function testRegionKey(string $getter, array $excludedIDs = []): void
    {
        $keys = [];
        foreach ($this->provideRegions() as [$region]) {
            if ($excludedIDs !== [] && in_array($region->getID(), $excludedIDs, true)) {
                continue;
            }
            $key = $region->{$getter}();
            $this->assertNotSame('', $key, "{$region->getID()} {$region}");
            $this->assertNotNull($key, "{$region->getID()} {$region}");
            $this->assertSame(false, in_array($key, $keys, true), "{$region->getID()} {$region}");
            $keys[] = $key;
        }
    }

    public function provideProvinceCases(): array
    {
        return [
            ['getID'],
            ['getName'],
            ['getOldID'],
            ['getVehicleCode'],
            ['getFiscalCode'],
            ['getNuts3'],
        ];
    }

    /**
     * @dataProvider provideProvinceCases
     */
    public function testProvinceKey(string $getter): void
    {
        $keys = [];
        foreach ($this->provideProvinces() as [$province]) {
            $key = $province->{$getter}();
            $this->assertNotSame('', $key, "{$province->getID()} {$province}");
            $this->assertNotNull($key, "{$province->getID()} {$province}");
            $this->assertSame(false, in_array($key, $keys, true), "{$province->getID()} {$province}");
            $keys[] = $key;
        }
    }

    public function provideMunicipalityCases(): array
    {
        return [
            ['getID'],
            ['getCadastralCode'],
            ['getFiscalCode', [
            ]],
            ['__toString'],
        ];
    }

    /**
     * @dataProvider provideMunicipalityCases
     */
    public function testMunicipalityKey(string $getter, array $excludedIDs = []): void
    {
        $keys = [];
        foreach ($this->provideMunicipalities() as [$municipality]) {
            if ($excludedIDs !== [] && in_array($municipality->getID(), $excludedIDs, true)) {
                continue;
            }
            $key = $municipality->{$getter}();
            $this->assertNotSame('', $key, "{$municipality->getID()} {$municipality}");
            $this->assertNotNull($key, "{$municipality->getID()} {$municipality}");
            $this->assertSame(false, in_array($key, $keys, true), "{$municipality->getID()} {$municipality}");
            $keys[] = $key;
        }
    }
}
