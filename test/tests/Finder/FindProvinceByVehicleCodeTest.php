<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test\Finder;

use MLocati\ComuniItaliani\Finder;
use MLocati\ComuniItaliani\Province;
use PHPUnit\Framework\TestCase;

class FindProvinceByVehicleCodeTest extends TestCase
{
    private static ?Finder $finder;

    public function provideGetProvinceByVehicleCodeCases(): array
    {
        return [
            ['', ''],
            ['0', ''],
            ['1', ''],
            ['AA', ''],
            ['AAAAAAA', ''],
            ['99', ''],
            ['Co', 'Como'],
            ['cO', 'Como'],
            ['co', 'Como'],
            ['CO', 'Como'],
            ['RM', 'Roma'],
        ];
    }

    /**
     * @dataProvider provideGetProvinceByVehicleCodeCases
     */
    public function testGetProvinceByVehicleCode(string $vehicleCode, string $expectedName): void
    {
        $province = $this->getFinder()->getProvinceByVehicleCode($vehicleCode);
        if ($expectedName === '') {
            $this->assertNull($province);
        } else {
            $this->assertInstanceOf(Province::class, $province);
            $this->assertSame($expectedName, (string) $province);
        }
    }

    private function getFinder(): Finder
    {
        return self::$finder ??= new Finder();
    }
}
