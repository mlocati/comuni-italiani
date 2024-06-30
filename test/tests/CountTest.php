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

class CountTest extends TerritoryTestCase
{
    public function testGeographicalSubdivisions(): void
    {
        $num = count(iterator_to_array($this->provideGeographicalSubdivisions()));
        $this->assertSame(5, $num);
    }

    public function testRegions(): void
    {
        $num = count(iterator_to_array($this->provideRegions()));
        $this->assertSame(20, $num);
    }

    public function testProvinces(): void
    {
        $num = count(iterator_to_array($this->provideProvinces()));
        $this->assertGreaterThanOrEqual((int) (107 * .9), $num);
        $this->assertLessThanOrEqual((int) (107 * 1.1), $num);
    }

    public function testMunicipalities(): void
    {
        $num = count(iterator_to_array($this->provideMunicipalities()));
        $this->assertGreaterThanOrEqual((int) (7896  * .9), $num);
        $this->assertLessThanOrEqual((int) (7896  * 1.1), $num);
    }
}
