<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test\Service;

use Generator;
use PHPUnit\Framework\TestCase;
use MLocati\ComuniItaliani\Factory;

abstract class TerritoryTestCase extends TestCase
{
    /**
     * @return \Generator<\MLocati\ComuniItaliani\GeographicalSubdivision>
     */
    public function provideGeographicalSubdivisions(): Generator
    {
        $factory = new Factory();
        foreach ($factory->getGeographicalSubdivisions() as $geographicalSubdivision) {
            yield [$geographicalSubdivision];
        }
    }

    /**
     * @return \Generator<\MLocati\ComuniItaliani\Region>
     */
    public function provideRegions(): Generator
    {
        $factory = new Factory();
        foreach ($factory->getRegions() as $region) {
            yield [$region];
        }
    }

    /**
     * @return \Generator<\MLocati\ComuniItaliani\Province>
     */
    public function provideProvinces(): Generator
    {
        $factory = new Factory();
        foreach ($factory->getProvinces() as $province) {
            yield [$province];
        }
    }

    /**
     * @return \Generator<\MLocati\ComuniItaliani\Municipality>
     */
    public function provideMunicipalities(): \Generator
    {
        $factory = new Factory();
        foreach ($factory->getMunicipalities() as $municipality) {
            yield [$municipality];
        }
    }
}
