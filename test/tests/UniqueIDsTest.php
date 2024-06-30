<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test;

use MLocati\ComuniItaliani\Test\Service\TerritoryTestCase;
use PHPUnit\Framework\TestCase;
use MLocati\ComuniItaliani\Factory;
use MLocati\ComuniItaliani\Municipality;
use MLocati\ComuniItaliani\TerritoryWithChildren;
use MLocati\ComuniItaliani\Region;
use MLocati\ComuniItaliani\Province;
use MLocati\ComuniItaliani\GeographicalSubdivision;

class UniqueIDsTest extends TerritoryTestCase
{
    public function testGeographicalSubdivisionIDs(): void
    {
        $already = [];
        foreach ($this->provideGeographicalSubdivisions() as [$geographicalSubdivision]) {
            $id = $geographicalSubdivision->getID();
            $this->assertSame(false, in_array($id, $already, true));
            $already[] = $id;
        }
    }

    public function testRegionIDs(): void
    {
        $already = [];
        foreach ($this->provideRegions() as [$region]) {
            $id = $region->getID();
            $this->assertSame(false, in_array($id, $already, true));
            $already[] = $id;
        }
    }

    public function testProvinceIDs(): void
    {
        $already = [];
        foreach ($this->provideProvinces() as [$province]) {
            $id = $province->getID();
            $this->assertSame(false, in_array($id, $already, true));
            $already[] = $id;
        }
    }

    public function testMunicipalityIDs(): void
    {
        $already = [];
        foreach ($this->provideMunicipalities() as [$municipality]) {
            $id = $municipality->getID();
            $this->assertSame(false, in_array($id, $already, true));
            $already[] = $id;
        }
    }
}
