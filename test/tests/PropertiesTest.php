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

class PropertiesTest extends TerritoryTestCase
{
    private const RX_VALID_NAMES = '{^\p{L}[\p{L} \-\/\'.]*\p{L}\'?$}u';

    /**
     * @dataProvider provideGeographicalSubdivisions
     */
    public function testGeographicalSubdivisionProperties(GeographicalSubdivision $geographicalSubdivision): void
    {
        $this->assertGreaterThanOrEqual(1, $geographicalSubdivision->getID());
        $this->assertMatchesRegularExpression(self::RX_VALID_NAMES, $geographicalSubdivision->getName());
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
        $this->assertMatchesRegularExpression(self::RX_VALID_NAMES, $region->getName());
        $this->assertSame($region->getName(), (string) $region);
        $this->assertContains($region->getType(), [
            Region\Type::ORDINARY_STATUS,
            Region\Type::SPECIAL_STATUS,
        ]);
        $this->assertMatchesRegularExpression('/^[0-9]{11}$/', $region->getFiscalCode());
        $this->assertNotSame('00000000000', $region->getFiscalCode());
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
        $this->assertMatchesRegularExpression(self::RX_VALID_NAMES, $province->getName());
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
        $this->assertMatchesRegularExpression('/^([0-9]{11})?$/', $province->getFiscalCode());
        $this->assertNotSame('00000000000', $province->getFiscalCode());
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
        $this->assertMatchesRegularExpression(self::RX_VALID_NAMES, $municipality->getName());
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
        $this->assertMatchesRegularExpression('/^([0-9]{11})?$/', $municipality->getFiscalCode());
        $this->assertNotSame('00000000000', $municipality->getFiscalCode());
        $this->assertMatchesRegularExpression('/^IT[A-Z]$/', $municipality->getNuts1());
        $this->assertMatchesRegularExpression('/^IT[A-Z][0-9]$/', $municipality->getNuts2());
        $this->assertMatchesRegularExpression('/^IT[A-Z][0-9][0-9A-Z]$/', $municipality->getNuts3());
    }
}
