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
}
