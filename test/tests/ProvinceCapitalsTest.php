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

class ProvinceCapitalsTest extends TerritoryTestCase
{
    private const EXPECTED = [
        'Napoli' => ['Napoli (NA)'],
        'Roma' => ['Roma (RM)'],
        'Milano' => ['Milano (MI)'],
        'Barletta-Andria-Trani' => ['Andria (BT)', 'Barletta (BT)', 'Trani (BT)'],
    ];

    public function testValidExpected(): void
    {
        $expected = self::EXPECTED;
        foreach ($this->provideProvinces() as [$province]) {
            unset($expected[(string) $province]);
        }
        $this->assertSame([], $expected);
    }

    /**
     * @dataProvider provideProvinces
     */
    public function testProvinceCapitals(Province $province): void
    {
        $capitals = $province->getCapitals();
        $this->assertGreaterThanOrEqual(1, count($capitals));
        $names = [];
        foreach ($capitals as $capital) {
            $this->assertInstanceOf(Municipality::class, $capital);
            $names[] = (string) $capital;
        }
        $key = (string) $province;
        if (isset(self::EXPECTED[$key])) {
            $this->assertSame(self::EXPECTED[$key], $names);
        }
    }
}
