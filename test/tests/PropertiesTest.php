<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test;

use MLocati\ComuniItaliani\Test\Service\TerritoryTestCase;
use MLocati\ComuniItaliani\Municipality;
use MLocati\ComuniItaliani\Region;
use MLocati\ComuniItaliani\Province;
use MLocati\ComuniItaliani\GeographicalSubdivision;
use MLocati\ComuniItaliani\Service\MultibyteTrait;

class PropertiesTest extends TerritoryTestCase
{
    use MultibyteTrait;

    private static string $rxValidNames;

    public static function setUpBeforeClass(): void
    {
        $mbChars = '';
        foreach (array_keys(self::getMultibyteToAsciiMap()) as $mbChar) {
            $mbChars .= $mbChar;
        }
        $letter = "A-Za-z{$mbChars}";
        self::$rxValidNames = "_^[{$letter}][{$letter} \-\/'.]*[$letter]'?\$_";
    }

    /**
     * @dataProvider provideGeographicalSubdivisions
     */
    public function testGeographicalSubdivisionProperties(GeographicalSubdivision $geographicalSubdivision): void
    {
        $this->assertGreaterThanOrEqual(1, $geographicalSubdivision->getID());
        $this->assertMatchesRegularExpression(self::$rxValidNames, $geographicalSubdivision->getName());
        $this->assertSame($geographicalSubdivision->getName(), (string) $geographicalSubdivision);
        $this->assertMatchesRegularExpression('/^IT[A-Z]$/', $geographicalSubdivision->getNuts1());
    }

    /**
     * @dataProvider provideRegions
     */
    public function testRegionProperties(Region $region): void
    {
        $this->assertMatchesRegularExpression('/^[0-9]{2}$/', $region->getID());
        $this->assertNotSame('00', $region->getID());
        $this->assertMatchesRegularExpression(self::$rxValidNames, $region->getName());
        $this->assertSame($region->getName(), (string) $region);
        $this->assertContains($region->getType(), [
            Region\Type::ORDINARY_STATUS,
            Region\Type::SPECIAL_STATUS,
        ]);
        self::testFiscalCode($region->getFiscalCode(), false);
        switch ($region->getID()) {
            case '04': // Trentino-Alto Adige/Südtirol
                $this->assertSame('', $region->getNuts2());
                break;
            default:
                $this->assertMatchesRegularExpression('/^IT[A-Z][0-9]$/', $region->getNuts2());
                break;
        }
    }

    /**
     * @dataProvider provideProvinces
     */
    public function testProvinceProperties(Province $province): void
    {
        $this->assertMatchesRegularExpression('/^[0-9]{3}$/', $province->getID());
        $this->assertNotSame('000', $province->getID());
        $this->assertMatchesRegularExpression(self::$rxValidNames, $province->getName());
        $this->assertSame($province->getName(), (string) $province);
        $this->assertMatchesRegularExpression('/^[0-9]{3}$/', $province->getOldID());
        $this->assertNotSame('000', $province->getOldID());
        $this->assertContains($province->getType(), [
            Province\Type::PROVINCE,
            Province\Type::AUTONOMOUS_PROVINCE,
            Province\Type::METROPOLITAN_CITY,
            Province\Type::FREE_CONSORTIUM_OF_MUNICIPALITIES,
            Province\Type::NON_ADMINISTRATIVE_UNIT,
        ]);
        $this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $province->getVehicleCode());
        self::testFiscalCode($province->getFiscalCode(), false);
        $this->assertMatchesRegularExpression('/^IT[A-Z][0-9][0-9A-Z]$/', $province->getNuts3());
    }

    /**
     * @dataProvider provideMunicipalities
     */
    public function testMunicipalityProperties(Municipality $municipality): void
    {
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $municipality->getID());
        $this->assertNotSame('000000', $municipality->getID());
        $this->assertStringStartsWith($municipality->getParent()->getOldID(), $municipality->getID());
        $this->assertMatchesRegularExpression(self::$rxValidNames, $municipality->getName());
        $this->assertSame("{$municipality->getName()} ({$municipality->getParent()->getVehicleCode()})", (string) $municipality);
        $nameIT = $municipality->getNameIT();
        if ($nameIT === '') {
            $this->assertSame('', $municipality->getNameForeign());
        } else {
            $this->assertNotSame($nameIT, $municipality->getNameForeign());
            $this->assertStringStartsWith($nameIT, $municipality->getName());
            $this->assertStringEndsWith($municipality->getNameForeign(), $municipality->getName());
        }
        $this->assertIsBool($municipality->isRegionalCapital());
        $this->assertIsBool($municipality->isProvinceCapital());
        $this->assertMatchesRegularExpression('/^[A-Z][0-9]{3}$/', $municipality->getCadastralCode());
        self::testFiscalCode($municipality->getFiscalCode(), false, "{$municipality->getID()} {$municipality}");
        $this->assertMatchesRegularExpression('/^IT[A-Z]$/', $municipality->getNuts1());
        $this->assertMatchesRegularExpression('/^IT[A-Z][0-9]$/', $municipality->getNuts2());
        $this->assertMatchesRegularExpression('/^IT[A-Z][0-9][0-9A-Z]$/', $municipality->getNuts3());
    }

    private static function testFiscalCode(string $fiscalCode, bool $optional, string $message = ''): void
    {
        if ($optional && $fiscalCode === '') {
            return;
        }
        static::assertMatchesRegularExpression('/^[0-9]{11}?$/', $fiscalCode, $message);
        static::assertNotSame('00000000000', $fiscalCode, $message);
        $sum = 0;
        for ($index = 0; $index <= 8; $index += 2) {
            $sum += (int) $fiscalCode[$index];
        }
        for ($index = 1; $index <= 9; $index += 2) {
            $s = ((int) $fiscalCode[$index]) << 1;
            if ($s > 9) {
                $s -= 9;
            }
            $sum += $s;
        }
        $check = $sum % 10;
        if ($check !== 0) {
            $check = 10 - $check;
        }
        static::assertSame($check, (int) $fiscalCode[10], $message);
    }
}
