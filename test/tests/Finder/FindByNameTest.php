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
            ['mano i lomb', []],
            ['mano i lomb', ['Romano di Lombardia (BG)'], true],
            ['mano i bg', []],
            ['mano i bg', ['Romano di Lombardia (BG)'], true],
            ['roman d bg', ['Romano di Lombardia (BG)']],
            ['ant su', ["Sant'Antioco (SU)", "Sant'Antonino di Susa (TO)"]],
            ['roma d lombard', ['Romano di Lombardia (BG)']],
            ['oma i ombard', []],
            ['oma i ombard', ['Romano di Lombardia (BG)'], true],
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

    private function getFinder(): Finder
    {
        return self::$finder ??= new Finder();
    }
}
