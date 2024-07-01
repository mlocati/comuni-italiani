<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test\Finder;

use MLocati\ComuniItaliani\Finder;
use MLocati\ComuniItaliani\Territory;
use MLocati\ComuniItaliani\GeographicalSubdivision;
use MLocati\ComuniItaliani\Region;
use MLocati\ComuniItaliani\Province;
use PHPUnit\Framework\TestCase;

class FindByIDTest extends TestCase
{
    private static ?Finder $finder;

    public function provideGetTerritoryByIDCases(): array
    {
        return [
            [null, ''],
            [[], ''],
            [-1, ''],
            [1, 'Nord-ovest'],
            [PHP_INT_MAX, ''],
            ['', ''],
            ['0', ''],
            ['1', ''],
            ['AA', ''],
            ['00', ''],
            ['01', 'Piemonte'],
            ['AAA', ''],
            ['000', ''],
            ['258', 'Roma'],
            ['058', ''],
            ['215', 'Milano'],
            ['015', ''],
            ['AAAA', ''],
            ['0123', ''],
            ['AAAAA', ''],
            ['01234', ''],
            ['AAAAAA', ''],
            ['000000', ''],
            ['058091', 'Roma (RM)'],
            ['258091', ''],
            ['015146', 'Milano (MI)'],
            ['215091', ''],
            ['0123456', ''],
            ['AAAAAAA', ''],
        ];
    }

    /**
     * @dataProvider provideGetTerritoryByIDCases
     */
    public function testGetTerritoryByID($id, string $expectedName): void
    {
        $territory = $this->getFinder()->getTerritoryByID($id);
        if ($expectedName === '') {
            $this->assertNull($territory);
        } else {
            $this->assertInstanceOf(Territory::class, $territory);
            $this->assertSame($expectedName, (string) $territory);
        }
    }

    public function provideGetGeographicalSubdivisionByIDCases(): array
    {
        return [
            [-1, ''],
            [1, 'Nord-ovest'],
            [PHP_INT_MAX, ''],
        ];
    }

    /**
     * @dataProvider provideGetGeographicalSubdivisionByIDCases
     */
    public function testGetGeographicalSubdivisionByID(int $id, string $expectedName): void
    {
        $territory = $this->getFinder()->getGeographicalSubdivisionByID($id);
        if ($expectedName === '') {
            $this->assertNull($territory);
        } else {
            $this->assertInstanceOf(GeographicalSubdivision::class, $territory);
            $this->assertSame($expectedName, (string) $territory);
        }
    }

    public function provideGetRegionByIDCases(): array
    {
        return [
            ['', ''],
            ['0', ''],
            ['1', ''],
            ['AA', ''],
            ['00', ''],
            ['01', 'Piemonte'],
            ['AAA', ''],
            ['000', ''],
            ['258', ''],
            ['058', ''],
            ['215', ''],
            ['015', ''],
            ['AAAA', ''],
            ['0123', ''],
            ['AAAAA', ''],
            ['01234', ''],
            ['AAAAAA', ''],
            ['000000', ''],
            ['058091', ''],
            ['258091', ''],
            ['015146', ''],
            ['215091', ''],
            ['0123456', ''],
            ['AAAAAAA', ''],
        ];
    }

    /**
     * @dataProvider provideGetRegionByIDCases
     */
    public function testGetRegionByID(string $id, string $expectedName): void
    {
        $territory = $this->getFinder()->getRegionByID($id);
        if ($expectedName === '') {
            $this->assertNull($territory);
        } else {
            $this->assertInstanceOf(Region::class, $territory);
            $this->assertSame($expectedName, (string) $territory);
        }
    }

    public function provideGetProvinceByIDCases(): array
    {
        return [
            ['', ''],
            ['0', ''],
            ['1', ''],
            ['AA', ''],
            ['00', ''],
            ['01', ''],
            ['AAA', ''],
            ['000', ''],
            ['258', 'Roma'],
            ['058', ''],
            ['058', 'Roma', true],
            ['215', 'Milano'],
            ['015', ''],
            ['015', 'Milano', true],
            ['AAAA', ''],
            ['0123', ''],
            ['AAAAA', ''],
            ['01234', ''],
            ['AAAAAA', ''],
            ['000000', ''],
            ['058091', ''],
            ['258091', ''],
            ['015146', ''],
            ['215091', ''],
            ['0123456', ''],
            ['AAAAAAA', ''],
        ];
    }

    /**
     * @dataProvider provideGetProvinceByIDCases
     */
    public function testGetProvinceByID(string $id, string $expectedName, bool $oldIDToo = false): void
    {
        $territory = $this->getFinder()->getProvinceByID($id, $oldIDToo);
        if ($expectedName === '') {
            $this->assertNull($territory);
        } else {
            $this->assertInstanceOf(Province::class, $territory);
            $this->assertSame($expectedName, (string) $territory);
        }
    }


    public function provideGetMunicipalityByIDCases(): array
    {
        return [
            ['', ''],
            ['0', ''],
            ['1', ''],
            ['AA', ''],
            ['00', ''],
            ['01', ''],
            ['AAA', ''],
            ['000', ''],
            ['258', ''],
            ['058', ''],
            ['215', ''],
            ['015', ''],
            ['AAAA', ''],
            ['0123', ''],
            ['AAAAA', ''],
            ['01234', ''],
            ['AAAAAA', ''],
            ['000000', ''],
            ['058091', 'Roma (RM)'],
            ['258091', ''],
            ['015146', 'Milano (MI)'],
            ['215091', ''],
            ['0123456', ''],
            ['AAAAAAA', ''],
        ];
    }

    /**
     * @dataProvider provideGetMunicipalityByIDCases
     */
    public function testGetMunicipalityByID(string $id, string $expectedName): void
    {
        $territory = $this->getFinder()->getMunicipalityByID($id);
        if ($expectedName === '') {
            $this->assertNull($territory);
        } else {
            $this->assertNotNull($territory);
            $this->assertSame($expectedName, (string) $territory);
        }
    }


    private function getFinder(): Finder
    {
        return self::$finder ??= new Finder();
    }
}
