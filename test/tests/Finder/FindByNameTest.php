<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test\Finder;

use MLocati\ComuniItaliani\Finder;
use PHPUnit\Framework\TestCase;

class FindByNameTest extends TestCase
{
    private static ?Finder $finder;

    public function provideFindGeographicalSubdivisionsByNameCases(): array
    {
        return [
            ['', []],
            ['Nord', ['Nord-ovest', 'Nord-est']],
            ['Nord Nord', []],
            ['Nord Ove', ['Nord-ovest']],
            ['Ove Nord', []],
            ['ord Ove', []],
            ['ord Ove', ['Nord-ovest'], true],
            ['IsOl', ['Isole']],
            ['Nord Nooo', []],
            ['ud', []],
            ['ud', ['Sud'], true],
            ["S\u{e0}d", []],
            ["S\u{f9}d", ['Sud']],
            ['o', ['Nord-ovest']],
            ['o', ['Nord-ovest', 'Nord-est', 'Centro', 'Isole'], true],
            ['z', []],
            ['Lombardia', []],
        ];
    }

    /**
     * @var string[] $expectedNames
     *
     * @dataProvider provideFindGeographicalSubdivisionsByNameCases
     */
    public function testFindGeographicalSubdivisionsByName(string $search, array $expectedNames, bool $allowMiddle = false): void
    {
        $territories = $this->getFinder()->findGeographicalSubdivisionsByName($search, $allowMiddle);
        $actualNames = array_map('strval', $territories);
        $this->assertSame($expectedNames, $actualNames);
    }

    public function provideFindRegionsByNameCases(): array
    {
        return [
            ['', []],
            ['z', []],
            ['Lazio', ['Lazio']],
            ['Laz', ['Lazio']],
            ['AZI', []],
            ['AZI', ['Lazio'], true],
            ['Nord', [], true],
            ["S\u{fc}dtirol", ["Trentino-Alto Adige/S\u{fc}dtirol"], true],
            ["Sudtirol", ["Trentino-Alto Adige/S\u{fc}dtirol"], true],
            ["\u{fc}dtirol", []],
            ["udtirol", []],
            ["\u{fc}dtirol", ["Trentino-Alto Adige/S\u{fc}dtirol"], true],
            ["udtirol", ["Trentino-Alto Adige/S\u{fc}dtirol"], true],
        ];
    }

    /**
     * @var string[] $expectedNames
     *
     * @dataProvider provideFindRegionsByNameCases
     */
    public function testFindRegionsByName(string $search, array $expectedNames, bool $allowMiddle = false): void
    {
        $territories = $this->getFinder()->findRegionsByName($search, $allowMiddle);
        $actualNames = array_map('strval', $territories);
        $this->assertSame($expectedNames, $actualNames);
    }

    public function provideFindProvincesByNameCases(): array
    {
        return [
            ['', []],
            ['z', []],
            ['erga', []],
            ['erga', ['Bergamo'], true],
            ['Nord', [], true],
            ['ozen', []],
            ['ozen', ['Bolzano/Bozen'], true],
            ['Bozen', ['Bolzano/Bozen']],
            ['Lombardia', [], true],
        ];
    }

    /**
     * @var string[] $expectedNames
     *
     * @dataProvider provideFindProvincesByNameCases
     */
    public function testFindProvincesByName(string $search, array $expectedNames, bool $allowMiddle = false): void
    {
        $territories = $this->getFinder()->findProvincesByName($search, $allowMiddle);
        $actualNames = array_map('strval', $territories);
        $this->assertSame($expectedNames, $actualNames);
    }

    public function provideFindMunicipalitiesByNameCases(): array
    {
        return [
            ['', []],
            ['ozzuoli', []],
            ['ozzuoli', ['Pozzuoli (NA)'], true],
            ['lombard roma', []],
            ['roma lombard', ['Romano di Lombardia (BG)']],
            ['roma d lombard', ['Romano di Lombardia (BG)']],
            ['mano i lomb', []],
            ['mano i lomb', ['Romano di Lombardia (BG)'], true],
            ['mano i bardi', []],
            ['mano i bardi', ['Romano di Lombardia (BG)'], true],
            ['roman d lom', ['Romano di Lombardia (BG)']],
            ['ant su', ["Sant'Antonino di Susa (TO)"]],
        ];
    }

    /**
     * @var string[] $expectedNames
     *
     * @dataProvider provideFindMunicipalitiesByNameCases
     */
    public function testFindMunicipalitiesByName(string $search, array $expectedNames, bool $allowMiddle = false): void
    {
        $territories = $this->getFinder()->findMunicipalitiesByName($search, $allowMiddle);
        $actualNames = array_map('strval', $territories);
        $this->assertSame($expectedNames, $actualNames);
    }

    public function testFindRestricted(): void
    {
        $finder = $this->getFinder();
        $geographicalSubdivision1 = $finder->findGeographicalSubdivisionsByName('nord ovest')[0];
        $this->assertSame('nord-ovest', strtolower((string) $geographicalSubdivision1));
        $geographicalSubdivision2 = $finder->findGeographicalSubdivisionsByName('sud')[0];
        $this->assertSame('sud', strtolower((string) $geographicalSubdivision2));
        $region1 = $finder->findRegionsByName('piemonte')[0];
        $this->assertSame('piemonte', strtolower((string) $region1));
        $region2 = $finder->findRegionsByName('puglia')[0];
        $this->assertSame('puglia', strtolower((string) $region2));
        $province1 = $finder->getProvinceByVehicleCode('to');
        $this->assertSame('torino', strtolower((string) $province1));
        $province2 = $finder->getProvinceByVehicleCode('ba');
        $this->assertSame('bari', strtolower((string) $province2));
        $municipality1 = null;
        foreach ($province1->getMunicipalities() as $m) {
            if ($municipality1 === null || strlen($m->getName()) > strlen($municipality1->getName())) {
                $municipality1 = $m;
            }
        }
        $this->assertNotSame('', (string) $municipality1);
        $municipality2 = null;
        foreach ($province2->getMunicipalities() as $m) {
            if ($municipality2 === null || strlen($m->getName()) > strlen($municipality2->getName())) {
                $municipality2 = $m;
            }
        }
        $this->assertNotSame('', (string) $municipality2);

        $found = array_map('strval', $finder->findRegionsByName($region1->getName()));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findRegionsByName($region1->getName(), false, $geographicalSubdivision1));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findRegionsByName($region1->getName(), false, $geographicalSubdivision2));
        $this->assertSame([], $found);

        $found = array_map('strval', $finder->findRegionsByName($region1->getName(), false, $region1));
        $this->assertSame([], $found);
        $found = array_map('strval', $finder->findRegionsByName($region1->getName(), false, $province1));
        $this->assertSame([], $found);

        $found = array_map('strval', $finder->findProvincesByName($province1->getName()));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findProvincesByName($province1->getName(), false, $geographicalSubdivision1));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findProvincesByName($province1->getName(), false, $geographicalSubdivision2));
        $this->assertSame([], $found);
        $found = array_map('strval', $finder->findProvincesByName($province1->getName(), false, $region1));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findProvincesByName($province1->getName(), false, $region2));
        $this->assertSame([], $found);
        $found = array_map('strval', $finder->findProvincesByName($province1->getName(), false, $province1));
        $this->assertSame([], $found);

        $found = array_map('strval', $finder->findMunicipalitiesByName($municipality1->getName()));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findMunicipalitiesByName($municipality1->getName(), false, $geographicalSubdivision1));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findMunicipalitiesByName($municipality1->getName(), false, $geographicalSubdivision2));
        $this->assertSame([], $found);
        $found = array_map('strval', $finder->findMunicipalitiesByName($municipality1->getName(), false, $region1));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findMunicipalitiesByName($municipality1->getName(), false, $region2));
        $this->assertSame([], $found);
        $found = array_map('strval', $finder->findMunicipalitiesByName($municipality1->getName(), false, $province1));
        $this->assertCount(1, $found);
        $found = array_map('strval', $finder->findMunicipalitiesByName($municipality1->getName(), false, $province2));
        $this->assertSame([], $found);
    }

    private function getFinder(): Finder
    {
        return self::$finder ??= new Finder();
    }
}
