<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test;

use MLocati\ComuniItaliani\Test\Service\TerritoryTestCase;
use MLocati\ComuniItaliani\Municipality;
use MLocati\ComuniItaliani\TerritoryWithChildren;
use MLocati\ComuniItaliani\Region;
use MLocati\ComuniItaliani\Province;
use MLocati\ComuniItaliani\GeographicalSubdivision;

class SingletonsTest extends TerritoryTestCase
{
    public function provideUnminifiedCases(): array
    {
        return [
            [false],
            [true],
        ];
    }

    /**
     * @dataProvider provideUnminifiedCases
     */
    public function testSingletons(bool $unminified): void
    {
        $factory = new Service\TestFactory();
        $factory->loadUnminified = $unminified;
        $geographicalSubdivisions = $factory->getGeographicalSubdivisions();
        $this->assertSame($geographicalSubdivisions, $factory->getGeographicalSubdivisions());
    }

    /**
     * @dataProvider provideGeographicalSubdivisions
     */
    public function testGeographicalSubdivision(GeographicalSubdivision $geographicalSubdivision): void
    {
        $this->assertNull($geographicalSubdivision->getParent());
        $children = $geographicalSubdivision->getChildren();
        $this->assertSame($children, $geographicalSubdivision->getRegions());
        foreach ($children as $child) {
            $this->assertSame($geographicalSubdivision, $child->getParent());
        }
    }

    /**
     * @dataProvider provideRegions
     */
    public function testRegion(Region $region): void
    {
        $this->assertInstanceOf(GeographicalSubdivision::class, $region->getParent());
        $children = $region->getChildren();
        $this->assertSame($children, $region->getProvinces());
        foreach ($children as $child) {
            $this->assertSame($region, $child->getParent());
        }
    }

    /**
     * @dataProvider provideProvinces
     */
    public function testProvince(Province $province): void
    {
        $this->assertInstanceOf(Region::class, $province->getParent());
        $children = $province->getChildren();
        $this->assertSame($children, $province->getMunicipalities());
        foreach ($children as $child) {
            $this->assertSame($province, $child->getParent());
        }
    }

    /**
     * @dataProvider provideMunicipalities
     */
    public function testMunicipality(Municipality $municipality): void
    {
        $this->assertInstanceOf(Province::class, $municipality->getParent());
        $this->assertNotInstanceOf(TerritoryWithChildren::class, $municipality);
    }
}
