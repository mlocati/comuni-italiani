<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test;

use MLocati\ComuniItaliani\Test\Service\TerritoryTestCase;
use MLocati\ComuniItaliani\Factory;
use MLocati\ComuniItaliani\Service\SorterTrait;
use MLocati\ComuniItaliani\Province;

class SortTest extends TerritoryTestCase
{
    use SorterTrait;

    public function testRegions(): void
    {
        $factory = new Factory();
        $this->testSort($factory->getRegions());
    }

    public function testProvinces(): void
    {
        $factory = new Factory();
        $this->testSort($factory->getProvinces());
    }

    public function testMunicipalities(): void
    {
        $factory = new Factory();
        $this->testSort($factory->getMunicipalities());
    }

    /**
     * @dataProvider provideProvinces
     */
    public function testMunicipalitiesAlreadySorted(Province $province): void
    {
        $this->testSort($province->getMunicipalities());
    }

    /**
     * @param \MLocati\ComuniItaliani\Territory[]
     */
    private function testSort(array $territories): void
    {
        $expectedNames = array_map('strval', $territories);
        $reversed = array_reverse($territories, false);
        $this->assertNotSame($territories, $reversed);
        $actualNames = array_map('strval', array_reverse($reversed, false));
        $this->assertSame($expectedNames, $actualNames);
        $actualNames = array_map('strval', $this->sortTerritoriesByName($reversed));
        $this->assertSame($expectedNames, $actualNames);
        $actualNames = array_map('strval', $this->sortTerritoriesByNameWithCollator($reversed));
        $this->assertSame($expectedNames, $actualNames);
        $actualNames = array_map('strval', $this->sortTerritoriesByNameWithoutCollator($reversed));
        $this->assertSame($expectedNames, $actualNames);
    }
}
