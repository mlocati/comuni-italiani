<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test;

use MLocati\ComuniItaliani\Test\Service\TerritoryTestCase;
use MLocati\ComuniItaliani\Municipality;
use MLocati\ComuniItaliani\Region;

class RegionCapitalsTest extends TerritoryTestCase
{
    private const EXPECTED = [
        'Lazio' => 'Roma (RM)',
        'Lombardia' => 'Milano (MI)',
        'Campania' => 'Napoli (NA)',
        'Piemonte' => 'Torino (TO)',
        'Sicilia' => 'Palermo (PA)',
    ];

    public function testValidExpected(): void
    {
        $expected = self::EXPECTED;
        foreach ($this->provideRegions() as [$region]) {
            unset($expected[(string) $region]);
        }
        $this->assertSame([], $expected);
    }

    /**
     * @dataProvider provideRegions
     */
    public function testRegionCapitals(Region $region): void
    {
        $capital = $region->getCapital();
        $this->assertInstanceOf(Municipality::class, $capital);
        $key = (string) $region;
        if (isset(self::EXPECTED[$key])) {
            $this->assertSame(self::EXPECTED[$key], (string) $capital);
        }
    }
}
